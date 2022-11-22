jQuery(document).ready(function($) {

    // Disable links inside layout previews
    $('.-preview a, .-preview button, .-preview input').click(e => {
        e.preventDefault();
    })
})