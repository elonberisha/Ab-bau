/**
 * Media Picker Logic
 * Përdoret për të hapur Media Library si popup dhe për të zgjedhur foto.
 */

// Global variable to store the currently targeted input ID
let currentTargetInputId = null;

/**
 * Hap Media Library në një dritare të re (Popup)
 * @param {string} inputId - ID e inputit ku do të vendoset URL e fotos
 */
function openMediaPicker(inputId) {
    currentTargetInputId = inputId;
    
    // Përmasat e dritares
    const width = 1000;
    const height = 700;
    const left = (window.screen.width - width) / 2;
    const top = (window.screen.height - height) / 2;
    
    // Hap dritaren
    window.open(
        'media-library.php?picker=true', 
        'MediaPicker', 
        `width=${width},height=${height},top=${top},left=${left},resizable=yes,scrollbars=yes`
    );
}

/**
 * Funksioni që thirret nga dritarja Popup kur zgjidhet një foto
 * @param {string} path - Rruga relative e fotos (p.sh. uploads/foto.jpg)
 */
window.handleMediaSelect = function(path) {
    if (currentTargetInputId) {
        const input = document.getElementById(currentTargetInputId);
        const preview = document.getElementById(currentTargetInputId + '_preview');
        
        if (input) {
            input.value = path;
            
            // Trigger change event nëse ka ndonjë listener
            input.dispatchEvent(new Event('change'));
        }
        
        // Përditëso preview nëse ekziston
        if (preview) {
            preview.src = '../' + path;
            // Hiq klasën 'hidden' ose stilin nëse është fshehur
            preview.style.display = 'block';
            
            // Nëse ka një container placeholder (si "No Image"), fshihe atë
            const placeholder = preview.parentElement.querySelector('.placeholder-text');
            if (placeholder) placeholder.style.display = 'none';
        }
    }
    currentTargetInputId = null;
};
