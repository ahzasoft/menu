    <!-- Cart Offcanvas -->
    <div class="offcanvas offcanvas-start" tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header text-white">
            <h5 class="offcanvas-title" id="cartOffcanvasLabel">سلة المشتريات</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div id="cart-items-container">
                <!-- Cart items will be injected here -->
                <p class="text-center text-muted my-5">سلة المشتريات فارغة</p>
            </div>
            
            <div id="cart-summary" class="d-none">
                <hr>
                <div class="d-flex justify-content-between fw-bold mb-3">
                    <span>الإجمالي:</span>
                    <span id="cart-total">0 جنيه</span>
                </div>
                
                <div class="mb-3">
                    <label class="form-label d-block">نوع الطلب</label>
                    <div class="d-flex flex-wrap gap-2" id="order-type-buttons">
                        <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1 order-type-btn active" data-value="pickup">استلام</button>
                        <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1 order-type-btn" data-value="takeaway">سفري</button>
                        <button type="button" class="btn btn-outline-primary btn-sm flex-grow-1 order-type-btn" data-value="table">طاولة</button>
                    </div>
                    <input type="hidden" id="order-type" value="pickup">
                </div>
                
                <div id="table-number-container" class="mb-3 d-none">
                    <label class="form-label">رقم الطاولة</label>
                    <select class="form-select" id="table-number">
                        <option value="1">طاولة 1</option>
                        <option value="2">طاولة 2</option>
                        <option value="3">طاولة 3</option>
                        <option value="4">طاولة 4</option>
                        <option value="5">طاولة 5</option>
                    </select>
                </div>

                <div class="d-grid gap-2">
                    <button class="btn btn-primary-custom py-3 fw-bold" onclick="checkout()">إتمام الطلب عبر واتساب</button>
                    <div class="d-flex gap-2">
                        <button class="btn btn-outline-danger flex-grow-1" onclick="clearCart()">إفراغ السلة</button>
                        <button class="btn btn-outline-secondary flex-grow-1" data-bs-dismiss="offcanvas">العودة للتسوق</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
