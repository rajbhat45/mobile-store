// cart_clean.js — fixed to use the real DB column name `id`
document.addEventListener("DOMContentLoaded", () => {
  if (window.__cartScriptLoaded) {
    console.warn("cart_clean.js already loaded – skipping second run");
    return;
  }
  window.__cartScriptLoaded = true;

  const cartContainer = document.querySelector(".cart-container");
  const shipping = 500;

  function getSummaryEls() {
    return {
      subtotalEl: document.querySelector(".cart-summary p:nth-of-type(1) strong"),
      totalEl: document.querySelector(".cart-summary p:nth-of-type(3) strong")
    };
  }



  function updateCartUI() {
    let subtotal = 0;
    document.querySelectorAll(".cart-item").forEach(item => {
      const price = parseFloat(item.querySelector(".unit-price")?.dataset.price || 0);
      const qtyInput = item.querySelector(".quantity input");
      const quantity = Math.max(1, parseInt(qtyInput?.value, 10) || 0);
      const itemTotal = price * quantity;

      const itemTotalEl = item.querySelector(".item-total");
      if (itemTotalEl) itemTotalEl.textContent = "₹" + itemTotal.toLocaleString();

      subtotal += itemTotal;
    });

    const { subtotalEl, totalEl } = getSummaryEls();
    if (subtotalEl) subtotalEl.textContent = "₹" + subtotal.toLocaleString();
    if (totalEl) totalEl.textContent = "₹" + (subtotal > 0 ? subtotal + shipping : 0).toLocaleString();
  }

  // remove button
  cartContainer.addEventListener("click", e => {
    const removeBtn = e.target.closest(".remove-btn");
    if (!removeBtn) return;

    const item = removeBtn.closest(".cart-item");
    const id = item.dataset.id || item.querySelector('input[name="id"]')?.value;
    if (!id) return;

    fetch("remove_from_cart.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `cart_id=${encodeURIComponent(id)}`

    })
    .then(() => {
      item.remove();     // remove from DOM
      updateCartUI();    // recalc totals
    })
    .catch(err => console.error("remove error", err));
  });


  // send updated quantity to PHP using `id`
  function persistQuantity(id, quantity) {
    fetch("update_cart.php", {
      method: "POST",
      headers: { "Content-Type": "application/x-www-form-urlencoded" },
      body: `id=${encodeURIComponent(id)}&quantity=${encodeURIComponent(quantity)}`
    }).catch(err => console.error("persistQuantity error", err));
  }

  if (!cartContainer) return;

  // plus/minus buttons
  cartContainer.addEventListener("click", e => {
    const minus = e.target.closest(".minus-btn");
    const plus = e.target.closest(".plus-btn");
    if (!minus && !plus) return;

    const item = e.target.closest(".cart-item");
    const qtyInput = item?.querySelector(".quantity input");
    if (!qtyInput) return;

    let current = parseInt(qtyInput.value, 10);
    if (isNaN(current) || current < 1) current = 1;
    if (minus && current > 1) current--;
    if (plus) current++;

    qtyInput.value = current;
    const id = item.dataset.id || item.querySelector('input[name="id"]')?.value;
    if (id) persistQuantity(id, current);

    updateCartUI();
  });

  // manual typing in number box
  cartContainer.addEventListener("input", e => {
    if (!e.target.matches(".quantity input")) return;
    let v = parseInt(e.target.value, 10);
    if (isNaN(v) || v < 1) v = 1;
    e.target.value = v;

    const item = e.target.closest(".cart-item");
    const id = item.dataset.id || item.querySelector('input[name="id"]')?.value;
    if (id) persistQuantity(id, v);

    updateCartUI();
  });

  // first paint
  updateCartUI();
});
