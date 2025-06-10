<?php
// Exit if accessed directly.
defined('ABSPATH') || exit;
?>

<style>
.loading-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    z-index: 9999;
    justify-content: center;
    align-items: center;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    position: relative;
}

.loading-spinner svg {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 40px;
    height: 40px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div id="loading-overlay" class="loading-overlay">
    <div class="loading-spinner">
        <svg width="232px" height="232px" viewBox="0 0 232 232" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
            <g id="Page-1" stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                <g id="icon">
                    <circle id="Fill-51" fill="#FECC2F" cx="116" cy="116" r="116"/>
                    <g id="Logo" transform="translate(53.855500, 74.762000)">
                        <path d="M122.1189,24.0899 L92.3619,24.0899 C88.8049,24.0899 85.9219,26.9729 85.9219,30.5299 C85.9219,34.0869 88.8049,36.9699 92.3619,36.9699 L114.8029,36.9699 C111.9789,47.1359 102.6419,54.6189 91.5889,54.6189 C88.0329,54.6189 85.1489,57.5029 85.1489,61.0599 C85.1489,64.6159 88.0329,67.4999 91.5889,67.4999 C101.4639,67.4999 110.7479,63.6539 117.7309,56.6719 C124.7139,49.6889 128.5589,40.4049 128.5589,30.5299 C128.5589,26.9729 125.6759,24.0899 122.1189,24.0899" id="Fill-126" fill="#36169A" fill-rule="nonzero"></path>
                        <path d="M61.0593,30.5303 C61.0593,47.3903 47.3913,61.0593 30.5303,61.0593 C13.6683,61.0593 0.0003,47.3903 0.0003,30.5303 C0.0003,13.6693 13.6683,0.0003 30.5303,0.0003 C47.3913,0.0003 61.0593,13.6693 61.0593,30.5303 Z" id="Stroke-127" stroke="#36169A" stroke-width="12.493" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M0.0002,30.5303 C0.0002,13.6693 13.6682,0.0003 30.5302,0.0003" id="Stroke-128" stroke="#1773F4" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M30.53,61.0596 C13.669,61.0596 0,47.3906 0,30.5306" id="Stroke-129" stroke="#1773F4" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M61.0593,30.5303 C61.0593,47.3903 47.3913,61.0593 30.5303,61.0593" id="Stroke-130" stroke="#36169A" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M67.4704,30.1084 C67.4704,33.6494 64.5994,36.5194 61.0594,36.5194 C57.5184,36.5194 54.6484,33.6494 54.6484,30.1084 C54.6484,26.5684 57.5184,23.6984 61.0594,23.6984 C64.5994,23.6984 67.4704,26.5684 67.4704,30.1084" id="Fill-131" fill="#36169A" fill-rule="nonzero"></path>
                        <path d="M61.0593,30.5303 C61.0593,13.6693 74.7283,0.0003 91.5893,0.0003" id="Stroke-132" stroke="#36169A" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M91.9431,0 C101.8751,0 110.6991,4.742 116.2741,12.087" id="Stroke-133" stroke="#36169A" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M91.5891,61.0596 C74.7281,61.0596 61.0591,47.3906 61.0591,30.5306" id="Stroke-134" stroke="#1773F4" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M122.7473,12.1885 C122.7473,15.7635 119.8493,18.6625 116.2743,18.6625 C112.6993,18.6625 109.8013,15.7635 109.8013,12.1885 C109.8013,8.6145 112.6993,5.7155 116.2743,5.7155 C119.8493,5.7155 122.7473,8.6145 122.7473,12.1885" id="Fill-135" fill="#1773F4" fill-rule="nonzero"></path>
                        <path d="M98.3537,30.5303 C98.3537,34.0703 95.4837,36.9403 91.9427,36.9403 C88.4027,36.9403 85.5327,34.0703 85.5327,30.5303 C85.5327,26.9893 88.4027,24.1193 91.9427,24.1193 C95.4837,24.1193 98.3537,26.9893 98.3537,30.5303" id="Fill-136" fill="#1773F4" fill-rule="nonzero"></path>
                        <path d="M30.53,0 C47.391,0 61.059,13.669 61.059,30.53" id="Stroke-137" stroke="#1773F4" stroke-width="13" stroke-linecap="round" stroke-linejoin="round"></path>
                    </g>
                </g>
            </g>
        </svg>
    </div>
</div>

<script>
function showLoadingOverlay() {
    document.getElementById('loading-overlay').style.display = 'flex';
}

function hideLoadingOverlay() {
    document.getElementById('loading-overlay').style.display = 'none';
}
</script> 