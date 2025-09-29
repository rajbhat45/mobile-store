document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.quantity input[name="quantity"]').forEach(inp => {
    console.log('On DOMContentLoaded input', inp.name, 'value=', inp.value);
  });
});


// cart.js — robust, final version
document.addEventListener("DOMContentLoaded", () => {
  const cartContainer = document.querySelector(".cart-container");
  const shipping = 500;

  function getSummaryEls() {
    const subtotalEl = document.querySelector(".cart-summary p:nth-of-type(1) strong");
    const totalEl = document.querySelector(".cart-summary p:nth-of-type(3) strong");
    return { subtotalEl, totalEl };
  }

// --- Debug: catch any changes to each quantity input ---
document.querySelectorAll('.quantity input[name="quantity"]').forEach(inp => {
  let old = inp.value;
  // Observe both attribute & property changes
  const obs = new MutationObserver(muts => {
    muts.forEach(m => console.log('MUTATION', inp.closest('.cart-item')?.dataset.cartId, 
                                  'old=', old, 'new attr=', inp.getAttribute('value')));
    old = inp.getAttribute('value');
  });
  obs.observe(inp, { attributes: true, attributeFilter: ['value'] });

  // Also trap JS property sets
  Object.defineProperty(inp, 'value', {
    set(v) {
      console.trace('VALUE-SETTER', inp.closest('.cart-item')?.dataset.cartId,
                    'old=', old, 'new prop=', v);
      old = v;
      HTMLInputElement.prototype.__lookupGetter__('value').call(inp); // keep native
    },
    get() {
      return HTMLInputElement.prototype.__lookupGetter__('value').call(inp);
    },
    configurable: true
  });
});


  function updateCartUI() {
    let subtotal = 0;
    const cartItems = document.querySelectorAll(".cart-item");

    // debug: how many cart rows the page actually has
    console.log('Cart rows count:', cartItems.length);

    cartItems.forEach(item => {
      // read numeric unit price from data attribute
      const priceEl = item.querySelector(".unit-price");
      const price = priceEl && priceEl.dataset && priceEl.dataset.price ? parseFloat(priceEl.dataset.price) || 0 : 0;

      // read quantity from the input
      const qtyInput = item.querySelector(".quantity input");
      const quantity = qtyInput ? Math.max(1, parseInt(qtyInput.value, 10) || 0) : 0;

      const itemTotal = price * quantity;

      // write item total
      const itemTotalEl = item.querySelector(".item-total");
      if (itemTotalEl) itemTotalEl.textContent = "₹" + itemTotal.toLocaleString();

      subtotal += itemTotal;

      // debug each item
      const cartId = item.dataset.cartId || (item.querySelector('input[name="cart_id"]')||{}).value || 'no-id';
      console.log(`cart-item[${cartId}] price=${price} qty=${quantity} itemTotal=${itemTotal}`);
    });

    // update summary
    const { subtotalEl, totalEl } = getSummaryEls();
    if (subtotalEl) subtotalEl.textContent = "₹" + subtotal.toLocaleString();
    if (totalEl) totalEl.textContent = "₹" + (subtotal > 0 ? (subtotal + shipping) : 0).toLocaleString();

    console.log(`Cart subtotal: ${subtotal}`);
  }

  // persist change to backend
  function persistQuantity(cartId, quantity) {
    fetch('update_cart.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `cart_id=${encodeURIComponent(cartId)}&quantity=${encodeURIComponent(quantity)}`
    })
    .then(r => r.json().catch(()=>({})))
    .then(data => {
      console.log('persistQuantity response', data);
      // we don't force a page reload; updateCartUI already shows new values
    })
    .catch(err => console.error('persistQuantity error', err));
  }

  if (!cartContainer) return;

  // handle plus/minus click (delegation)
  cartContainer.addEventListener("click", (e) => {
    const minusBtn = e.target.closest(".minus-btn");
    const plusBtn = e.target.closest(".plus-btn");
    if (!minusBtn && !plusBtn) return;

    const item = e.target.closest(".cart-item");
    if (!item) return;

    const qtyInput = item.querySelector(".quantity input");
    if (!qtyInput) return;

    // read fresh value
    let current = parseInt(qtyInput.value, 10);
    if (isNaN(current) || current < 1) current = 1;

    if (minusBtn && current > 1) current--;
    else if (plusBtn) current++;

    // update input then persist
    qtyInput.value = current;

    const cartId = item.dataset.cartId || (item.querySelector('input[name="cart_id"]')||{}).value;
    if (cartId) {
      console.log(`Persisting cartId=${cartId}, newQty=${current}`);
      persistQuantity(cartId, current);
    }

    updateCartUI();
  });

  // handle manual input changes
  cartContainer.addEventListener("input", (e) => {
    if (!e.target.matches(".quantity input")) return;
    let v = parseInt(e.target.value, 10);
    if (isNaN(v) || v < 1) v = 1;
    e.target.value = v;

    const item = e.target.closest(".cart-item");
    const cartId = item.querySelector('input[name="cart_id"]')?.value || item.dataset.cartId;
    if (cartId) persistQuantity(cartId, v);

    updateCartUI();
  });

  // initial render
  updateCartUI();
});
