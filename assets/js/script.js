$(document).ready(function() {
    // Handle like buttons
    $('.like-btn').on('click', function() {
        const btn = $(this);
        const type = btn.data('type');
        const id = btn.data('id');
        
        let url = type === 'question' 
            ? '/qa-platform/questions/like_question.php' 
            : '/qa-platform/answers/like_answer.php';
        
        let data = type === 'question' 
            ? { question_id: id } 
            : { answer_id: id };
        
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Update like count
                    $(`#${type}-likes-${id}`).text(response.likes);
                    
                    // Disable button
                    btn.prop('disabled', true);
                    btn.html('<i class="bi bi-check-circle"></i>');
                    
                    // Show success message
                    showNotification('Success! Your like has been recorded.', 'success');
                } else {
                    showNotification(response.message, 'warning');
                }
            },
            error: function() {
                showNotification('An error occurred. Please try again.', 'danger');
            }
        });
    });
    
    // Notification function
    function showNotification(message, type) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show position-fixed" 
                 style="top: 80px; right: 20px; z-index: 9999; min-width: 300px;" 
                 role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        $('body').append(alertHtml);
        
        setTimeout(function() {
            $('.alert').fadeOut(300, function() {
                $(this).remove();
            });
        }, 3000);
    }
    
    // Form validation
    $('form').on('submit', function(e) {
        const form = $(this);
        const requiredFields = form.find('[required]');
        let isValid = true;
        
        requiredFields.each(function() {
            if ($(this).val().trim() === '') {
                isValid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            showNotification('Please fill in all required fields.', 'danger');
        }
    });
    
    // Remove validation class on input
    $('input, textarea, select').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
    
    // Auto-resize textarea
    $('textarea').on('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});