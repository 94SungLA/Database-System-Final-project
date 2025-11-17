<!-- Review Modal -->
<style>
    .star {
        color: gold;
    }
</style>
<div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-lg mx-4">
        <h3 class="text-lg font-bold mb-4 text-gray-800">評價任務</h3>
        <form id="reviewForm">
            <input type="hidden" name="task_id" id="modalTaskId">
            <input type="hidden" name="reviewee_id" id="modalRevieweeId">
            <input type="hidden" name="role" id="modalRole">
            <input type="hidden" name="rating" id="modalRating" value="">
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">評分 (1-5)</label>
                <div id="starRating" class="flex space-x-1">
                    <span class="star text-2xl cursor-pointer" data-rating="1">☆</span>
                    <span class="star text-2xl cursor-pointer" data-rating="2">☆</span>
                    <span class="star text-2xl cursor-pointer" data-rating="3">☆</span>
                    <span class="star text-2xl cursor-pointer" data-rating="4">☆</span>
                    <span class="star text-2xl cursor-pointer" data-rating="5">☆</span>
                </div>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">評論</label>
                <textarea name="comment" id="modalComment" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500" placeholder="請輸入評論"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" id="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">取消</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">提交</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('reviewModal');
        const form = document.getElementById('reviewForm');
        const closeBtn = document.getElementById('closeModal');
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('modalRating');
        const commentInput = document.getElementById('modalComment');

        // Open modal
        function openModal(taskId, revieweeId, role) {
            document.getElementById('modalTaskId').value = taskId;
            document.getElementById('modalRevieweeId').value = revieweeId;
            document.getElementById('modalRole').value = role;
            modal.classList.remove('hidden');
        }

        // Close modal
        function closeModal() {
            modal.classList.add('hidden');
            form.reset();
            stars.forEach(s => s.textContent = '☆');
        }

        closeBtn.addEventListener('click', closeModal);
        modal.addEventListener('click', function(e) {
            if (e.target === modal) closeModal();
        });

        // Handle form submit
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Add validation
            if (!ratingInput.value || ratingInput.value < 1 || ratingInput.value > 5) {
                alert('請選擇有效的評分 (1-5)');
                return;
            }
            if (!commentInput.value.trim()) {
                alert('請輸入評論');
                return;
            }
            const formData = new FormData(form);
            fetch('/Database-System-Final-project/backend/review.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        closeModal();
                        location.reload(); // Refresh to update UI
                    } else {
                        alert(data.message);
                    }
                })
                .catch(error => {
                    alert('提交失敗，請重試');
                });
        });

        // Handle star rating
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                stars.forEach(s => {
                    s.textContent = s.dataset.rating <= rating ? '★' : '☆';
                });
            });
        });

        // Attach to review buttons (modify existing links to buttons)
        document.querySelectorAll('.review-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                const taskId = this.dataset.taskId;
                const revieweeId = this.dataset.revieweeId;
                const role = this.dataset.role;
                openModal(taskId, revieweeId, role);
            });
        });
    });
</script>