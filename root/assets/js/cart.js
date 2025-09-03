(function () {
  "use strict";

  // Small helper that tries to use your existing showFlashMessage(), otherwise falls back to alert()
  function flash(msg, type = 'success') {
    if (typeof showFlashMessage === 'function') {
      showFlashMessage(msg, type);
      return;
    }
    // fallback
    if (type === 'success') window.alert('✅ ' + msg);
    else window.alert('❌ ' + msg);
  }

  // Attach a single delegated listener (prevents accidental double binding)
  document.addEventListener('DOMContentLoaded', () => {
    document.body.addEventListener('click', async (ev) => {
      const btn = ev.target.closest('.add-to-cart');
      if (!btn) return;

      ev.preventDefault();

      // Prevent double-click / concurrent requests for this button
      if (btn.dataset.loading === '1') return;
      btn.dataset.loading = '1';

      // Visual feedback
      const originalHTML = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';

      const productId = btn.dataset.productId;
      if (!productId) {
        btn.dataset.loading = '0';
        btn.disabled = false;
        btn.innerHTML = originalHTML;
        flash('Product ID missing', 'error');
        return;
      }

      try {
        // DEBUG: you can uncomment this console.log to see requests in devtools
        // console.log('Sending add-to-cart for', productId);

        const resp = await fetch('/api/cart.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `action=add&product_id=${encodeURIComponent(productId)}&quantity=${encodeURIComponent(btn.dataset.quantity || 1)}`
        });

        // If the endpoint returns non-JSON or 500, this will throw
        const data = await resp.json();

        if (data && data.success) {
          // update header cart count if present
          const cartCountEl = document.getElementById('cart-count') || document.querySelector('.cart-count');
          if (cartCountEl && typeof data.cart_count !== 'undefined') {
            cartCountEl.textContent = data.cart_count;
          }

          flash(data.message || 'Added to cart', 'success');
        } else {
          // server responded with success=false
          const msg = (data && data.message) ? data.message : 'Could not add to cart';
          flash(msg, 'error');
        }
      } catch (err) {
        console.error('Add to cart error:', err);
        flash('Something went wrong (see console)', 'error');
      } finally {
        // restore button state
        btn.dataset.loading = '0';
        btn.disabled = false;
        btn.innerHTML = originalHTML;
      }
    }, false);
  });
})();