/* =========================================================
   TheSlowMatcha - Checkout Flow Scripts (vanilla JS)
   Dipakai bersama oleh: cart, checkout, shipping, payment,
   success, track.
   ========================================================= */

document.addEventListener('DOMContentLoaded', function () {
  initQtyControls();
  initSelectableOptions();
  initTrackSearch();
});

/**
 * Kontrol jumlah item di keranjang (tombol - / +).
 * Elemen butuh struktur:
 * <div class="tsm-qty" data-price="420000">
 *   <button type="button" data-action="decrease">-</button>
 *   <input type="number" min="1" value="1">
 *   <button type="button" data-action="increase">+</button>
 * </div>
 * Baris item pembungkus diberi class "tsm-item" dan punya
 * elemen ".tsm-item__price" untuk menampilkan harga x qty.
 */
function initQtyControls() {
  document.querySelectorAll('.tsm-qty').forEach(function (qty) {
    var input = qty.querySelector('input');
    var decreaseBtn = qty.querySelector('[data-action="decrease"]');
    var increaseBtn = qty.querySelector('[data-action="increase"]');
    var unitPrice = parseInt(qty.dataset.price || '0', 10);
    var itemRow = qty.closest('.tsm-item');
    var priceEl = itemRow ? itemRow.querySelector('.tsm-item__price') : null;

    function updatePrice() {
      if (!priceEl || !unitPrice) return;
      var qtyVal = parseInt(input.value, 10) || 1;
      priceEl.textContent = formatRupiah(unitPrice * qtyVal);
      recalculateSummary();
    }

    if (decreaseBtn) {
      decreaseBtn.addEventListener('click', function () {
        var val = Math.max(1, (parseInt(input.value, 10) || 1) - 1);
        input.value = val;
        updatePrice();
      });
    }

    if (increaseBtn) {
      increaseBtn.addEventListener('click', function () {
        var val = (parseInt(input.value, 10) || 1) + 1;
        input.value = val;
        updatePrice();
      });
    }

    if (input) {
      input.addEventListener('change', function () {
        if ((parseInt(input.value, 10) || 0) < 1) input.value = 1;
        updatePrice();
      });
    }
  });

  // Tombol hapus item
  document.querySelectorAll('.tsm-item__remove').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var row = btn.closest('.tsm-item');
      if (row) {
        row.remove();
        recalculateSummary();
      }
    });
  });
}

/**
 * Hitung ulang subtotal & total di ringkasan belanja
 * berdasarkan harga tiap item yang sedang tampil.
 * Ringkasan butuh elemen:
 * <span data-summary="subtotal">Rp 0</span>
 * <span data-summary="total">Rp 0</span>
 * Ongkos kirim (jika ada) diambil dari data-shipping-cost
 * pada elemen ringkasan (default 0).
 */
function recalculateSummary() {
  var subtotalEl = document.querySelector('[data-summary="subtotal"]');
  var totalEl = document.querySelector('[data-summary="total"]');
  if (!subtotalEl && !totalEl) return;

  var subtotal = 0;
  document.querySelectorAll('.tsm-item').forEach(function (row) {
    var priceEl = row.querySelector('.tsm-item__price');
    if (priceEl) subtotal += parseRupiah(priceEl.textContent);
  });

  var shippingCost = 0;
  var summaryBox = document.querySelector('[data-shipping-cost]');
  if (summaryBox) shippingCost = parseInt(summaryBox.dataset.shippingCost, 10) || 0;

  if (subtotalEl) subtotalEl.textContent = formatRupiah(subtotal);
  if (totalEl) totalEl.textContent = formatRupiah(subtotal + shippingCost);
}

/**
 * Opsi yang bisa dipilih (radio) untuk pengiriman & pembayaran.
 * Klik di mana saja pada ".tsm-option" akan menandai radio
 * di dalamnya dan memberi class "is-selected".
 */
function initSelectableOptions() {
  document.querySelectorAll('.tsm-option').forEach(function (option) {
    var radio = option.querySelector('input[type="radio"]');
    if (!radio) return;

    function select() {
      var groupName = radio.name;
      document.querySelectorAll('input[name="' + groupName + '"]').forEach(function (r) {
        r.closest('.tsm-option').classList.remove('is-selected');
      });
      radio.checked = true;
      option.classList.add('is-selected');
    }

    option.addEventListener('click', select);
    if (radio.checked) option.classList.add('is-selected');
  });
}

/**
 * Form pencarian di halaman "Lacak Pesanan".
 * Cukup contoh submit handler; ganti dengan request
 * ke server (fetch / form submit biasa) sesuai kebutuhan.
 */
function initTrackSearch() {
  var form = document.querySelector('[data-track-form]');
  if (!form) return;

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    var input = form.querySelector('input[name="order_number"]');
    if (!input || !input.value.trim()) {
      input.focus();
      return;
    }
    // TODO: arahkan ke route pencarian pesanan, contoh:
    // window.location.href = '/lacak-pesanan/' + encodeURIComponent(input.value.trim());
    form.submit();
  });
}

/* ---------- Helpers ---------- */
function formatRupiah(number) {
  return 'Rp ' + Math.round(number).toLocaleString('id-ID');
}

function parseRupiah(text) {
  return parseInt(String(text).replace(/[^0-9]/g, ''), 10) || 0;
}
