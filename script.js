// ===== CONFIG =====
const WHATSAPP_NUMBER = "781151579";
const MAIL_CONTACT = "contact@techshop.sn";

// ===== PRODUITS =====
let products = [];

fetch("get_product.php")
  .then(res => res.json())
  .then(data => {
      products = data;

      var page = document.body.getAttribute("data-page");

      if (page === "home") {
          var params = new URLSearchParams(window.location.search);
          renderHome(params.get("q") || "");
      }

      if (page === "categories") {
          renderCategories(null);
      }
  })
  .catch(err => {
      console.error("Erreur chargement produits :", err);
  });
const categories = ["Informatique", "Audio", "Smartphones", "Montres"];
const categoryIcons = { Informatique: "&#128187;", Audio: "&#127911;", Smartphones: "&#128241;", Montres: "&#8987;" };

function formatCFA(price) {
  return price.toLocaleString("fr-FR") + " F CFA";
}

function starsHTML(rating) {
  return Array.from({ length: 5 }, function(_, i) {
    return '<span class="star ' + (i < rating ? 'on' : 'off') + '">&#9733;</span>';
  }).join("");
}

function whatsappURL(name, price) {
  var msg = "Bonjour TechShop, je souhaite commander :\n\n" + name + "\nPrix : " + formatCFA(price) + "\n\nMerci de confirmer la disponibilite.";
  return "https://wa.me/" + WHATSAPP_NUMBER + "?text=" + encodeURIComponent(msg);
}

function productCardHTML(p) {
  var btn = p.inStock
    ? '<a href="' + whatsappURL(p.name, p.price) + '" target="_blank" class="btn-whatsapp">📱 Commander via WhatsApp</a>'
    : '<div class="out-of-stock">Rupture de stock</div>';

  return '<div class="product-card">' +
    '<div class="product-img">' +
      '<img src="' + p.img + '" alt="' + p.name + '" loading="lazy">' +
    '</div>' +
    '<div class="product-body">' +
      '<p class="product-name">' + p.name + '</p>' +
      '<p class="product-desc">' + p.desc + '</p>' +
      '<div class="stars">' + starsHTML(p.rating) + '<span class="reviews">(' + p.reviews + ')</span></div>' +
      '<p class="product-price">Prix : <span>' + formatCFA(p.price) + '</span></p>' +
      btn +
    '</div>' +
  '</div>';
}


function renderHome(query) {
  var main = document.getElementById("main-content");
  if (!main) return;
  var lq = (query || "").toLowerCase();
  var filtered = lq ? products.filter(function(p) {
    return p.name.toLowerCase().indexOf(lq) >= 0 ||
           p.desc.toLowerCase().indexOf(lq) >= 0 ||
           p.category.toLowerCase().indexOf(lq) >= 0;
  }) : null;

  var html = "";
  if (lq) {
    html += '<div class="search-banner"><span>' + (filtered ? filtered.length : 0) + ' resultat(s) pour <strong>' + query + '</strong></span><a href="index.html">Effacer</a></div>';
  }

  var displayCats = filtered ? categories.filter(function(c) { return filtered.some(function(p) { return p.category === c; }); }) : categories;
  displayCats.forEach(function(cat) {
    var list = (filtered || products).filter(function(p) { return p.category === cat; });
    if (!list.length) return;
    html += '<div class="section-gap">' +
      '<div class="cat-header"><h2 class="cat-title">' + cat + '</h2><div class="cat-line"></div></div>' +
      '<div class="product-grid">' + list.map(productCardHTML).join("") + '</div>' +
    '</div>';
  });

  if (filtered && filtered.length === 0) {
    html = '<div style="text-align:center;padding:80px 0;color:#9ca3af">Aucun produit trouve pour &laquo; ' + query + ' &raquo;<br><a href="index.html" style="color:#f59e0b;text-decoration:underline">Voir tous les produits</a></div>';
  }
  main.innerHTML = html;
}

function renderCategories(selectedCat) {
  var sidebar = document.getElementById("sidebar-list");
  var grid = document.getElementById("cat-grid");
  if (!sidebar || !grid) return;

  var sidebarHTML = '<li class="sidebar-item"><button class="sidebar-btn ' + (!selectedCat ? 'active' : '') + '" onclick="renderCategories(null)">&#128230; Tous les produits<span class="count">' + products.length + '</span></button></li>';
  categories.forEach(function(cat) {
    var count = products.filter(function(p) { return p.category === cat; }).length;
    sidebarHTML += '<li class="sidebar-item"><button class="sidebar-btn ' + (selectedCat === cat ? 'active' : '') + '" onclick="renderCategories(\'' + cat + '\')">' + categoryIcons[cat] + ' ' + cat + '<span class="count">' + count + '</span></button></li>';
  });
  sidebar.innerHTML = sidebarHTML;

  var list = selectedCat ? products.filter(function(p) { return p.category === selectedCat; }) : products;
  grid.innerHTML = '<div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:20px"><h2 style="color:#1a1f2e">' + (selectedCat || "Tous les produits") + '</h2><span style="color:#9ca3af;font-size:.85rem">' + list.length + ' produit(s)</span></div><div class="product-grid">' + list.map(productCardHTML).join("") + '</div>';
}

document.addEventListener("DOMContentLoaded", function() {
  var overlay = document.getElementById("search-overlay");
  var btnOpen = document.getElementById("btn-search-open");
  var form = document.getElementById("search-form");
  var btn = document.getElementById("hamburger");
  var menu = document.getElementById("mobile-menu");

  if (btnOpen) btnOpen.addEventListener("click", function() { overlay.classList.add("open"); });
  if (overlay) overlay.addEventListener("click", function(e) { if (e.target === overlay) overlay.classList.remove("open"); });
  document.addEventListener("keydown", function(e) { if (e.key === "Escape" && overlay) overlay.classList.remove("open"); });
  if (form) {
    form.addEventListener("submit", function(e) {
      e.preventDefault();
      var q = document.getElementById("search-input").value.trim();
      if (q) window.location.href = "index.html?q=" + encodeURIComponent(q);
    });
  }
  if (btn && menu) btn.addEventListener("click", function() { menu.classList.toggle("open"); });

  var page = document.body.getAttribute("data-page");
  if (page === "home") {
    var params = new URLSearchParams(window.location.search);
    renderHome(params.get("q") || "");
  }
  if (page === "categories") renderCategories(null);
});
