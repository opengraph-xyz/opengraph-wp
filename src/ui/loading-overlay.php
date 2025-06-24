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

.loading-container {
    position: relative;
    width: 50px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.loading-spinner {
    position: absolute;
    width: 50px;
    height: 50px;
    border: 5px solid #f3f3f3;
    border-top: 5px solid #3498db;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

.loading-logo {
    position: relative;
    z-index: 1;
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 50%;
    overflow: hidden;
}

.loading-logo img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>

<div id="loading-overlay" class="loading-overlay">
    <div class="loading-container">
        <div class="loading-spinner"></div>
        <div class="loading-logo">
            <img src="<?php echo esc_url(plugin_dir_url(__FILE__) . '../../assets/icon-128x128.png'); ?>" alt="Loading..." />
        </div>
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