document.addEventListener('DOMContentLoaded', function () {
    const buttons = document.querySelectorAll('.category-button');
    buttons.forEach(button => {
        button.addEventListener('click', function () {
            const category = this.getAttribute('data-category');
            fetchProducts(category);
        });
    });
});

function fetchProducts(category) {
    fetch(`${ajaxurl}?action=fetch_products&category=${category}`)
        .then(response => response.json())
        .then(data => {
            const productGrid = document.getElementById('product-grid');
            productGrid.innerHTML = '';
            data.products.forEach(product => {
                const productElement = document.createElement('div');
                productElement.classList.add('product-item');
                productElement.innerHTML = `
                    <img src="${product.image}" alt="${product.title}">
                    <h2>${product.title}</h2>
                    <p>${product.description}</p>
                    <p>${product.price}</p>
                    <button onclick="addToCart(${product.id})">Comprar</button>
                `;
                productGrid.appendChild(productElement);
            });
        });
}

function addToCart(productId) {
    fetch(`/?add-to-cart=${productId}`)
        .then(response => response.json())
        .then(data => {
            alert('Producto agregado al carrito');
        });
}
