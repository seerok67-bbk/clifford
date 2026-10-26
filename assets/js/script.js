/**
 * SineSine - Client-side Interactions
 */

$(document).ready(function() {
    // Rating star selector interaction on review forms
    $('.star-rating-btn').on('click', function(e) {
        e.preventDefault();
        const score = $(this).data('rating');
        $('#rating_input').val(score);
        $('#selected_rating_label').text(score + '/10');
        
        $('.star-rating-btn').removeClass('active');
        $('.star-rating-btn').each(function() {
            if ($(this).data('rating') <= score) {
                $(this).addClass('active');
            }
        });
    });

    // Auto-dismiss alerts after 5 seconds
    setTimeout(function() {
        $('.alert-dismissible').fadeOut('slow');
    }, 5000);

    // Image preview for avatar upload
    $('#avatar_input').on('change', function(e) {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $('#avatar_preview').attr('src', event.target.result);
            };
            reader.readAsDataURL(file);
        }
    });

    // Image preview for movie poster upload
    $('#poster_input').on('change', function(e) {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $('#poster_preview').attr('src', event.target.result).show();
            };
            reader.readAsDataURL(file);
        }
    });
});

