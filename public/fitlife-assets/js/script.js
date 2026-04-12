'use strict';



/**
 * add event on element
 */

const addEventOnElem = function (elem, type, callback) {
  if (elem.length > 1) {
    for (let i = 0; i < elem.length; i++) {
      elem[i].addEventListener(type, callback);
    }
  } else {
    elem.addEventListener(type, callback);
  }
}



/**
 * navbar toggle
 */

const navbar = document.querySelector("[data-navbar]");
const navTogglers = document.querySelectorAll("[data-nav-toggler]");
const navLinks = document.querySelectorAll("[data-nav-link]");

const toggleNavbar = function () { navbar.classList.toggle("active"); }

addEventOnElem(navTogglers, "click", toggleNavbar);

const closeNavbar = function () { navbar.classList.remove("active"); }

addEventOnElem(navLinks, "click", closeNavbar);



/**
 * header & back top btn active
 */

const header = document.querySelector("[data-header]");
const backTopBtn = document.querySelector("[data-back-top-btn]");
const coursesLoader = document.querySelector("[data-courses-loading]");
const coursesGrid = document.querySelector("[data-courses-grid]");

window.addEventListener("scroll", function () {
  if (!header) return;
  if (window.scrollY >= 100) {
    header.classList.add("active");
    backTopBtn.classList.add("active");
  } else {
    header.classList.remove("active");
    backTopBtn.classList.remove("active");
  }
});

window.addEventListener("load", function () {
  if (coursesGrid) {
    coursesGrid.classList.add("ready");
  }
  if (coursesLoader) {
    coursesLoader.classList.add("hidden");
  }
});



/**
 * course content accordion
 */

const accordionLists = document.querySelectorAll(".course-accordion");

const setAccordionIcon = function (item, isOpen) {
  const icon = item.querySelector(".accordion-meta ion-icon");
  if (!icon) return;
  icon.setAttribute("name", isOpen ? "chevron-up-outline" : "chevron-down-outline");
}

accordionLists.forEach(function (list) {
  const items = Array.from(list.children).filter(function (child) {
    return child.tagName === "LI";
  });

  items.forEach(function (item) {
    const header = item.querySelector(".accordion-header");
    if (!header) return;

    // Accessibility: make the clickable header keyboard-operable.
    header.setAttribute("role", "button");
    header.setAttribute("tabindex", "0");

    setAccordionIcon(item, item.classList.contains("is-open"));

    const toggleItem = function () {
      const willOpen = !item.classList.contains("is-open");

      items.forEach(function (otherItem) {
        otherItem.classList.remove("is-open");
        setAccordionIcon(otherItem, false);
      });

      item.classList.toggle("is-open", willOpen);
      setAccordionIcon(item, willOpen);
    };

    header.addEventListener("click", toggleItem);

    header.addEventListener("keydown", function (event) {
      if (event.key === "Enter" || event.key === " ") {
        event.preventDefault();
        toggleItem();
      }
    });
  });
});



/**
 * courses filter, sort and pagination
 */

const paginationRoot = document.querySelector("[data-courses-pagination]");

if (paginationRoot && coursesGrid) {
  const cards = Array.from(coursesGrid.querySelectorAll(".course-card"));
  const sizeSelect = paginationRoot.querySelector("[data-pagination-size]");
  const resultsLabel = paginationRoot.querySelector("[data-pagination-results]");
  const pagesWrap = paginationRoot.querySelector("[data-pagination-pages]");
  const prevBtn = paginationRoot.querySelector("[data-pagination-prev]");
  const nextBtn = paginationRoot.querySelector("[data-pagination-next]");
  const coursesCount = document.querySelector("[data-courses-count]");
  const emptyState = document.querySelector("[data-courses-empty]");
  const filterRoot = document.querySelector("[data-filter-root]");
  const filterToggle = document.querySelector("[data-filter-toggle]");
  const filterMenu = document.querySelector("[data-filter-menu]");
  const filterLevel = document.querySelector("[data-filter-level]");
  const filterStatus = document.querySelector("[data-filter-status]");
  const filterReset = document.querySelector("[data-filter-reset]");
  const sortRoot = document.querySelector("[data-sort-root]");
  const sortToggle = document.querySelector("[data-sort-toggle]");
  const sortMenu = document.querySelector("[data-sort-menu]");
  const sortSelect = document.querySelector("[data-sort-select]");

  let pageSize = parseInt(sizeSelect ? sizeSelect.value : "16", 10);
  let currentPage = 1;
  let activeCards = cards.slice();
  let selectedLevel = "all";
  let selectedStatus = "all";
  let selectedSort = "default";

  cards.forEach(function (card, index) {
    card.dataset.originalIndex = String(index);
  });

  const toInt = function (value) {
    const parsed = parseInt(value || "0", 10);
    return Number.isNaN(parsed) ? 0 : parsed;
  };

  const getProgressStatus = function (value) {
    const progress = toInt(value);
    if (progress <= 0) return "not_started";
    if (progress >= 100) return "completed";
    return "in_progress";
  };

  const openMenu = function (menu, toggle) {
    if (!menu || !toggle) return;
    menu.hidden = false;
    toggle.classList.add("is-active");
  };

  const closeMenu = function (menu, toggle) {
    if (!menu || !toggle) return;
    menu.hidden = true;
    toggle.classList.remove("is-active");
  };

  const closeAllMenus = function () {
    closeMenu(filterMenu, filterToggle);
    closeMenu(sortMenu, sortToggle);
  };

  const sortCards = function (items) {
    const sorted = items.slice();

    sorted.sort(function (a, b) {
      if (selectedSort === "title_asc") {
        return (a.dataset.title || "").localeCompare(b.dataset.title || "");
      }
      if (selectedSort === "title_desc") {
        return (b.dataset.title || "").localeCompare(a.dataset.title || "");
      }
      if (selectedSort === "lessons_desc") {
        return toInt(b.dataset.lessons) - toInt(a.dataset.lessons);
      }
      if (selectedSort === "lessons_asc") {
        return toInt(a.dataset.lessons) - toInt(b.dataset.lessons);
      }
      if (selectedSort === "progress_desc") {
        return toInt(b.dataset.progress) - toInt(a.dataset.progress);
      }
      if (selectedSort === "progress_asc") {
        return toInt(a.dataset.progress) - toInt(b.dataset.progress);
      }

      return toInt(a.dataset.originalIndex) - toInt(b.dataset.originalIndex);
    });

    return sorted;
  };

  const applyFilterAndSort = function (resetPage) {
    const filtered = cards.filter(function (card) {
      const level = (card.dataset.level || "").toLowerCase();
      const status = getProgressStatus(card.dataset.progress);

      if (selectedLevel !== "all" && level !== selectedLevel) return false;
      if (selectedStatus !== "all" && status !== selectedStatus) return false;

      return true;
    });

    activeCards = sortCards(filtered);

    const activeSet = new Set(activeCards);
    const inactiveCards = cards.filter(function (card) {
      return !activeSet.has(card);
    });

    activeCards.concat(inactiveCards).forEach(function (card) {
      coursesGrid.appendChild(card);
    });

    if (resetPage) currentPage = 1;
    updateCards();
  };

  const getPageItems = function (totalPages, activePage) {
    if (totalPages <= 7) {
      return Array.from({ length: totalPages }, function (_, i) { return i + 1; });
    }

    const items = [1];
    const start = Math.max(2, activePage - 1);
    const end = Math.min(totalPages - 1, activePage + 1);

    if (start > 2) items.push("...");
    for (let i = start; i <= end; i++) items.push(i);
    if (end < totalPages - 1) items.push("...");

    items.push(totalPages);
    return items;
  };

  const updateCards = function () {
    const total = activeCards.length;
    const totalPages = Math.max(1, Math.ceil(Math.max(total, 1) / pageSize));
    currentPage = Math.min(Math.max(1, currentPage), totalPages);

    const startIndex = (currentPage - 1) * pageSize;
    const endIndex = Math.min(startIndex + pageSize, total);
    const visibleCards = activeCards.slice(startIndex, endIndex);
    const visibleSet = new Set(visibleCards);

    cards.forEach(function (card, index) {
      card.style.display = visibleSet.has(card) ? "" : "none";
    });

    if (resultsLabel) {
      const first = total === 0 ? 0 : startIndex + 1;
      const last = total === 0 ? 0 : endIndex;
      resultsLabel.textContent = "Results: " + first + " - " + last + " of " + total;
    }

    if (coursesCount) {
      coursesCount.textContent = String(total);
    }

    if (emptyState) {
      emptyState.hidden = total !== 0;
    }

    if (prevBtn) {
      prevBtn.classList.toggle("disabled", currentPage === 1);
      prevBtn.disabled = currentPage === 1 || total === 0;
    }

    if (nextBtn) {
      nextBtn.classList.toggle("disabled", currentPage === totalPages);
      nextBtn.disabled = currentPage === totalPages || total === 0;
    }

    if (!pagesWrap) return;

    pagesWrap.innerHTML = "";
    if (total === 0) return;

    const pageItems = getPageItems(totalPages, currentPage);

    pageItems.forEach(function (item) {
      if (item === "...") {
        const dots = document.createElement("span");
        dots.className = "page-dots";
        dots.textContent = "...";
        pagesWrap.appendChild(dots);
        return;
      }

      const pageBtn = document.createElement("button");
      pageBtn.type = "button";
      pageBtn.className = "btn-page" + (item === currentPage ? " active-page" : "");
      pageBtn.textContent = String(item);
      pageBtn.addEventListener("click", function () {
        currentPage = item;
        updateCards();
      });
      pagesWrap.appendChild(pageBtn);
    });
  };

  if (sizeSelect) {
    sizeSelect.addEventListener("change", function () {
      const parsed = parseInt(sizeSelect.value, 10);
      pageSize = Number.isNaN(parsed) || parsed < 1 ? 16 : parsed;
      currentPage = 1;
      updateCards();
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", function () {
      if (currentPage <= 1) return;
      currentPage--;
      updateCards();
    });
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", function () {
      const totalPages = Math.max(1, Math.ceil(Math.max(activeCards.length, 1) / pageSize));
      if (currentPage >= totalPages) return;
      currentPage++;
      updateCards();
    });
  }

  if (filterToggle && filterMenu && filterRoot) {
    filterToggle.addEventListener("click", function (event) {
      event.stopPropagation();
      const opening = filterMenu.hidden;
      closeAllMenus();
      if (opening) openMenu(filterMenu, filterToggle);
    });
  }

  if (sortToggle && sortMenu && sortRoot) {
    sortToggle.addEventListener("click", function (event) {
      event.stopPropagation();
      const opening = sortMenu.hidden;
      closeAllMenus();
      if (opening) openMenu(sortMenu, sortToggle);
    });
  }

  if (filterMenu) {
    filterMenu.addEventListener("click", function (event) {
      event.stopPropagation();
    });
  }

  if (sortMenu) {
    sortMenu.addEventListener("click", function (event) {
      event.stopPropagation();
    });
  }

  document.addEventListener("click", function () {
    closeAllMenus();
  });

  document.addEventListener("keydown", function (event) {
    if (event.key === "Escape") closeAllMenus();
  });

  if (filterLevel) {
    filterLevel.addEventListener("change", function () {
      selectedLevel = filterLevel.value || "all";
      applyFilterAndSort(true);
    });
  }

  if (filterStatus) {
    filterStatus.addEventListener("change", function () {
      selectedStatus = filterStatus.value || "all";
      applyFilterAndSort(true);
    });
  }

  if (filterReset) {
    filterReset.addEventListener("click", function () {
      selectedLevel = "all";
      selectedStatus = "all";
      if (filterLevel) filterLevel.value = "all";
      if (filterStatus) filterStatus.value = "all";
      applyFilterAndSort(true);
    });
  }

  if (sortSelect) {
    sortSelect.addEventListener("change", function () {
      selectedSort = sortSelect.value || "default";
      applyFilterAndSort(true);
    });
  }

  applyFilterAndSort(false);
}
