<!-- Review Modal -->
<style>
    .star {
        color: gold;
        transition: color 0.3s ease, transform 0.3s ease, text-shadow 0.3s ease;
    }

    .star:hover {
        color: #ffd700;
        transform: scale(1.2);
        text-shadow: 0 0 10px #ffd700;
    }

    .star.selected {
        color: #ffed4e;
        text-shadow: 0 0 15px #ffed4e;
    }

    #reviewModal {
        backdrop-filter: blur(5px);
    }

    #modalContent {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3), 0 0 20px rgba(0, 123, 255, 0.1);
        border-radius: 20px;
    }

    #starRating .star {
        font-size: 2.5rem;
    }

    textarea {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    textarea:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.3);
    }

    button {
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    button:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }
</style>
<div id="reviewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden flex items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl p-8 w-full max-w-lg mx-4 transform transition-all duration-300 ease-in-out scale-95 opacity-0" id="modalContent">
        <h3 class="text-xl font-bold mb-6 text-gray-800 text-center">評價任務</h3>
        <form id="reviewForm">
            <input type="hidden" name="task_id" id="modalTaskId">
            <input type="hidden" name="reviewee_id" id="modalRevieweeId">
            <input type="hidden" name="role" id="modalRole">
            <input type="hidden" name="rating" id="modalRating" value="">
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">評分 (1-5)</label>
                <div id="starRating" class="flex space-x-2 justify-center">
                    <span class="star text-3xl cursor-pointer" data-rating="1">☆</span>
                    <span class="star text-3xl cursor-pointer" data-rating="2">☆</span>
                    <span class="star text-3xl cursor-pointer" data-rating="3">☆</span>
                    <span class="star text-3xl cursor-pointer" data-rating="4">☆</span>
                    <span class="star text-3xl cursor-pointer" data-rating="5">☆</span>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">評論</label>
                <textarea name="comment" id="modalComment" rows="4" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 resize-none" placeholder="請輸入評論"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" id="closeModal" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-all duration-200 font-medium">取消</button>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 font-medium shadow-md">提交</button>
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

        // Open modal with animation
        function openModal(taskId, revieweeId, role) {
            document.getElementById('modalTaskId').value = taskId;
            document.getElementById('modalRevieweeId').value = revieweeId;
            document.getElementById('modalRole').value = role;
            modal.classList.remove('hidden');
            setTimeout(() => {
                document.getElementById('modalContent').classList.remove('scale-95', 'opacity-0');
                document.getElementById('modalContent').classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        // Close modal with animation
        function closeModal() {
            document.getElementById('modalContent').classList.remove('scale-100', 'opacity-100');
            document.getElementById('modalContent').classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
                form.reset();
                stars.forEach(s => {
                    s.textContent = '☆';
                    s.classList.remove('selected');
                });
            }, 300);
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

        // Handle star rating with visual feedback
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.dataset.rating;
                ratingInput.value = rating;
                stars.forEach(s => {
                    if (s.dataset.rating <= rating) {
                        s.textContent = '★';
                        s.classList.add('selected');
                    } else {
                        s.textContent = '☆';
                        s.classList.remove('selected');
                    }
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