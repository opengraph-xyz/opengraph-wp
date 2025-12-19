jQuery( document ).ready( function ( $ ) {
	const $container = $( '#opengraph-xyz-filters-container' );
	if ( ! $container.length ) return;

	let groups = $container.data( 'filters' ) || [];
	if ( ! Array.isArray( groups ) ) {
		groups = [];
	}
	// Clean up empty groups if any
	if ( groups.length === 1 && groups[ 0 ].length === 0 ) {
		groups = [];
	}

	const operators = {
		string: [
			{ value: 'begins_with', label: 'Begins with' },
			{ value: 'ends_with', label: 'Ends with' },
			{ value: 'contains', label: 'Contains' },
			{ value: 'exact', label: 'Exactly matches' },
			{ value: 'not_begins_with', label: 'Does not begin with' },
			{ value: 'not_ends_with', label: 'Does not end with' },
			{ value: 'not_contains', label: 'Does not contain' },
			{ value: 'not_exact', label: 'Does not exactly match' },
		],
		array: [ { value: 'in', label: 'Is' } ],
	};

	const fields = [
		{ value: 'post_title', label: 'Post Title', type: 'string' },
		{ value: 'post_author', label: 'Post Author', type: 'array' },
		{ value: 'category', label: 'Category', type: 'array' },
		{ value: 'post_tag', label: 'Tag', type: 'array' },
		{ value: 'published_date', label: 'Published Date', type: 'date' },
		{ value: 'modified_date', label: 'Modified Date', type: 'date' },
	];

	function render() {
		$container.empty();

		if ( groups.length === 0 ) {
			const $addFilter = $(
				'<button type="button" class="button button-primary">Add Filter</button>'
			);
			$addFilter.click( () => {
				groups.push( [
					{ field: 'post_title', operator: 'contains', value: '' },
				] );
				render();
			} );
			$container.append( $addFilter );
			updateHiddenInput();
			return;
		}

		groups.forEach( ( group, groupIndex ) => {
			const $group = $( '<div class="og-filter-group"></div>' );

			if ( groupIndex > 0 ) {
				$container.append(
					'<div class="og-filter-separator"><span>AND</span></div>'
				);
			}

			const $conditions = $( '<div class="og-filter-conditions"></div>' );

			group.forEach( ( condition, conditionIndex ) => {
				if ( conditionIndex > 0 ) {
					$conditions.append(
						'<div class="og-condition-separator"><span>OR</span></div>'
					);
				}
				$conditions.append(
					renderCondition(
						condition,
						groupIndex,
						conditionIndex,
						conditionIndex === group.length - 1
					)
				);
			} );

			$group.append( $conditions );

			$container.append( $group );
		} );

		const $addGroup = $(
			'<button type="button" class="button button-secondary og-add-group">AND</button>'
		);
		$addGroup.click( () => {
			groups.push( [
				{ field: 'post_title', operator: 'contains', value: '' },
			] );
			render();
		} );
		$container
			.append( '<div class="og-actions"></div>' )
			.find( '.og-actions' )
			.append( $addGroup );

		updateHiddenInput();
	}

	function renderCondition( condition, groupIndex, conditionIndex, isLast ) {
		const $row = $( '<div class="og-filter-row"></div>' );

		// Field Select
		const $fieldSelect = $(
			'<select name="opengraph-field" class="og-field-select" aria-label="Field"></select>'
		);
		fields.forEach( ( f ) => {
			$fieldSelect.append(
				`<option value="${ f.value }" ${
					f.value === condition.field ? 'selected' : ''
				} data-type="${ f.type }">${ f.label }</option>`
			);
		} );
		$fieldSelect.change( function () {
			const newField = $( this ).val();
			const type = $( this ).find( ':selected' ).data( 'type' );
			condition.field = newField;
			// Reset operator if type changes
			const availableOps =
				type === 'array' ? operators.array : operators.string; // Simplified
			condition.operator = availableOps[ 0 ].value;
			condition.value = '';
			render();
		} );
		$row.append( $fieldSelect );

		// Operator Select
		const fieldType =
			fields.find( ( f ) => f.value === condition.field )?.type ||
			'string';
		const $operatorSelect = $(
			'<select name="opengraph-operator" class="og-operator-select" aria-label="Operator"></select>'
		);
		let ops = [];
		if ( fieldType === 'array' ) ops = operators.array;
		else if ( fieldType === 'date' )
			ops = [
				{ value: 'after', label: 'Is after' },
				{ value: 'before', label: 'Is before' },
			];
		// Custom for date
		else ops = operators.string;

		ops.forEach( ( op ) => {
			$operatorSelect.append(
				`<option value="${ op.value }" ${
					op.value === condition.operator ? 'selected' : ''
				}>${ op.label }</option>`
			);
		} );
		$operatorSelect.change( function () {
			condition.operator = $( this ).val();
			render();
		} );
		$row.append( $operatorSelect );

		// Value Input
		let $valueInput;
		if (
			[ 'exact', 'not_exact', 'in', 'not_in' ].includes(
				condition.operator
			) &&
			[ 'category', 'post_tag', 'post_author' ].includes(
				condition.field
			)
		) {
			// Popup trigger for categories/tags/authors
			$valueInput = $( '<div class="og-value-wrapper"></div>' );
			const $display = $(
				'<input name="opengraph-value" type="text" readonly class="og-value-display" placeholder="Select terms..." aria-label="Term Selector">'
			);
			$display.val(
				Array.isArray( condition.value )
					? condition.value.map( ( v ) => v.name ).join( ', ' )
					: condition.value
			);

			// Auto-size width based on content
			const val = $display.val();
			if ( val.length > 0 ) {
				$display.css( 'width', val.length + 'ch' );
			}

			const $btn = $(
				'<button type="button" class="button">Select</button>'
			);
			$btn.click( function () {
				// Open modal to select terms
				openTermSelector(
					condition.field,
					condition.value,
					( selected ) => {
						condition.value = selected; // Expecting array of {id, name}
						render();
					}
				);
			} );
			$valueInput.append( $display ).append( $btn );
		} else if ( fieldType === 'date' ) {
			$valueInput = $(
				'<input name="opengraph-value" type="date"  class="og-value-input" >'
			);
			$valueInput.val( condition.value );
			$valueInput.change( function () {
				condition.value = $( this ).val();
				updateHiddenInput();
			} );
		} else {
			$valueInput = $(
				'<input name="opengraph-value" type="text" class="og-value-input">'
			);
			$valueInput.val( condition.value );
			$valueInput.change( function () {
				condition.value = $( this ).val();
				updateHiddenInput();
			} );
		}
		$row.append( $valueInput );

		const $actionsWrapper = $( '<div class="og-condition-actions"></div>' );

		if ( isLast ) {
			const $orBtn = $(
				'<button type="button" class="button button-secondary og-add-condition-inline">OR</button>'
			);
			$orBtn.click( () => {
				groups[ groupIndex ].push( {
					field: 'post_title',
					operator: 'contains',
					value: '',
				} );
				render();
			} );
			$actionsWrapper.append( $orBtn );
		}

		// Remove button
		const $remove = $(
			'<button type="button" class="dashicons dashicons-remove og-remove-condition" aria-label="Remove condition"></button>'
		);
		$remove.click( () => {
			groups[ groupIndex ].splice( conditionIndex, 1 );
			if ( groups[ groupIndex ].length === 0 ) {
				groups.splice( groupIndex, 1 );
			}
			render();
		} );
		$actionsWrapper.append( $remove );

		$row.append( $actionsWrapper );

		return $row;
	}

	function updateHiddenInput() {
		$( '#opengraph-xyz-filters-data' )
			.val( JSON.stringify( groups ) )
			.trigger( 'change' );
	}

	// Mock Term Selector for now - needs real implementation fetching WP terms
	function openTermSelector( taxonomy, current, callback ) {
		// This should be a modal. For now, let's use a simple prompt or a custom div overlay.
		// In a real WP plugin, we'd probably use a thickbox or a custom modal with AJAX search.
		// For this task, I'll implement a simple modal.

		let title = 'Select Terms';
		if ( taxonomy === 'category' ) title = 'Select Categories';
		else if ( taxonomy === 'post_tag' ) title = 'Select Tags';
		else if ( taxonomy === 'post_author' ) title = 'Select Authors';

		const $modal = $( `
            <div class="og-modal-overlay">
                <div class="og-modal">
                    <h3>${ title }</h3>
                    <div class="og-term-search">
                        <input type="text" placeholder="Search..." id="og-term-search-input">
                    </div>
                    <div class="og-term-list">Loading...</div>
                    <div class="og-modal-actions">
                        <button class="button button-primary og-modal-save">Save</button>
                        <button class="button og-modal-cancel">Cancel</button>
                    </div>
                </div>
            </div>
        ` );

		$( 'body' ).append( $modal );

		// Fetch terms via AJAX
		$.ajax( {
			url: ajaxurl,
			data: {
				action: 'opengraph_fetch_terms',
				taxonomy: taxonomy,
			},
			success: function ( response ) {
				if ( response.success ) {
					const terms = response.data;
					const $list = $modal.find( '.og-term-list' );
					$list.empty();
					terms.forEach( ( term ) => {
						const isChecked =
							Array.isArray( current ) &&
							current.find( ( c ) => c.id == term.term_id );
						$list.append( `
                            <label class="og-term-item">
                                <input type="checkbox" value="${
									term.term_id
								}" data-name="${ term.name }" ${
									isChecked ? 'checked' : ''
								}>
                                ${ term.name }
                            </label>
                        ` );
					} );
				}
			},
		} );

		$modal.find( '.og-modal-cancel' ).click( () => $modal.remove() );
		$modal.find( '.og-modal-save' ).click( () => {
			const selected = [];
			$modal.find( 'input:checked' ).each( function () {
				selected.push( {
					id: $( this ).val(),
					name: $( this ).data( 'name' ),
				} );
			} );
			callback( selected );
			$modal.remove();
		} );
	}

	render();
} );
