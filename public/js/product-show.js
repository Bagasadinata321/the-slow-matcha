let selectedVariantId = null;
let maxStock = 0;

function switchMainImage(src, element, label) {
    const mainImg = document.getElementById('mainImage');
    const labelSpan = document.getElementById('galleryLabel');
    
    if (mainImg) mainImg.src = src;
    if (labelSpan && label) labelSpan.textContent = label;

    document.querySelectorAll('.thumb-item').forEach(item => {
        item.classList.remove('active');
    });
    element.classList.add('active');
}

function selectVariant(element) {
    document.querySelectorAll('.size-pill').forEach(pill => {
        pill.classList.remove('active');
    });
    element.classList.add('active');

    const price = parseFloat(element.dataset.price);
    const promo = parseFloat(element.dataset.promo);
    const hasPromo = element.dataset.hasPromo === 'true';
    const stock = parseInt(element.dataset.stock);

    selectedVariantId = element.dataset.variantId;
    maxStock = stock;

    const priceBlock = document.getElementById('priceBlock');
    const stockBlock = document.getElementById('stockDisplay');

    if (hasPromo && promo > 0) {
        priceBlock.innerHTML = `
            <span>Rp ${promo.toLocaleString('id-ID')}</span>
            <span style="text-decoration: line-through; color: #888; font-size: 16px; margin-left: 8px;">
                Rp ${price.toLocaleString('id-ID')}
            </span>
        `;
    } else {
        priceBlock.innerHTML = `Rp ${price.toLocaleString('id-ID')}`;
    }

    if (stockBlock) {
        stockBlock.innerText = stock > 0 ? 'Ready Stock' : 'Out of Stock';
    }

    const qtyInput = document.getElementById('qtyInput');
    if (qtyInput) qtyInput.value = 1;
}

document.addEventListener('DOMContentLoaded', function () {
    const qtyInput = document.getElementById('qtyInput');
    const qtyMinus = document.getElementById('qtyMinus');
    const qtyPlus = document.getElementById('qtyPlus');

    if (qtyMinus && qtyPlus && qtyInput) {
        qtyMinus.addEventListener('click', function () {
            let current = parseInt(qtyInput.value) || 1;
            if (current > 1) qtyInput.value = current - 1;
        });

        qtyPlus.addEventListener('click', function () {
            let current = parseInt(qtyInput.value) || 1;
            if (current < maxStock) qtyInput.value = current + 1;
        });
    }

    // Tab Navigation
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabPanels = document.querySelectorAll('.tab-panel');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const target = this.dataset.tab;

            tabBtns.forEach(b => b.classList.remove('active'));
            tabPanels.forEach(p => p.classList.remove('active'));

            this.classList.add('active');
            const targetPanel = document.getElementById(target);
            if (targetPanel) targetPanel.classList.add('active');
        });
    });

    // Auto Select First Variant
    const firstPill = document.querySelector('.size-pill');
    if (firstPill) {
        selectVariant(firstPill);
    }
});