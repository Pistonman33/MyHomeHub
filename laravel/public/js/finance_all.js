document.addEventListener("DOMContentLoaded", function () {
  const csrfToken =
    document
      .querySelector('meta[name="csrf-token"]')
      ?.getAttribute("content") || "";
  const categories = JSON.parse(
    document.getElementById("finance-categories-json")?.textContent || "[]",
  );
  const saveAllButton = document.getElementById("saveAllFinanceChanges");

  const buildCategoryOptions = function (selectedId) {
    return categories
      .map(function (category) {
        const isSelected =
          String(category.id) === String(selectedId) ? "selected" : "";
        return (
          '<option value="' +
          category.id +
          '" ' +
          isSelected +
          ">" +
          category.name +
          "</option>"
        );
      })
      .join("");
  };

  const restoreButton = function (button, original) {
    const replacement = document.createElement("button");
    replacement.type = "button";
    replacement.className = original.className;
    replacement.dataset.field = original.dataset.field;
    replacement.dataset.recordId = original.dataset.recordId;
    replacement.dataset.value = original.dataset.value || "";
    replacement.dataset.categoryId = original.dataset.categoryId || "";
    replacement.dataset.categoryName = original.dataset.categoryName || "";
    replacement.innerHTML = original.innerHTML;
    button.replaceWith(replacement);
    return replacement;
  };

  const markRowDirty = function (row) {
    if (row) {
      row.classList.add("is-dirty");
      row.dataset.dirty = "1";
    }
  };

  const unmarkRowDirty = function (row) {
    if (row) {
      row.classList.remove("is-dirty");
      row.dataset.dirty = "0";
    }
  };

  const refreshRowFromButtons = function (row) {
    const libelleButton = row.querySelector('[data-field="libelle"]');
    const categoryButton = row.querySelector('[data-field="category"]');
    if (libelleButton) {
      row.dataset.libelle =
        libelleButton.dataset.value || libelleButton.textContent.trim();
    }
    if (categoryButton) {
      row.dataset.categoryId = categoryButton.dataset.categoryId || "";
    }
  };

  const collectDirtyRows = function () {
    return Array.from(document.querySelectorAll(".finance-row.is-dirty")).map(
      function (row) {
        refreshRowFromButtons(row);
        return {
          id: row.dataset.recordId,
          libelle: row.dataset.libelle || "",
          category_id: row.dataset.categoryId || "",
        };
      },
    );
  };

  const submitInlineEdit = function (button, field, value) {
    const row = button.closest(".finance-row");
    if (field === "libelle") {
      button.dataset.value = value;
      row.dataset.libelle = value;
    }
    if (field === "category") {
      button.dataset.categoryId = value || "";
      row.dataset.categoryId = value || "";
    }
    markRowDirty(row);
  };

  if (saveAllButton) {
    saveAllButton.addEventListener("click", function () {
      const dirtyRows = collectDirtyRows();
      if (!dirtyRows.length) {
        return;
      }

      const saveQueue = dirtyRows.map(function (item) {
        const formData = new URLSearchParams();
        formData.append("_token", csrfToken);
        formData.append("id", item.id);
        formData.append("libelle", item.libelle || "");
        formData.append("category_id", item.category_id || "");
        return fetch("/admin/finance/all/update", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8",
            "X-CSRF-TOKEN": csrfToken,
            Accept: "application/json",
          },
          body: formData.toString(),
        }).then(function (response) {
          return response.json();
        });
      });

      Promise.all(saveQueue)
        .then(function () {
          document
            .querySelectorAll(".finance-row.is-dirty")
            .forEach(function (row) {
              unmarkRowDirty(row);
            });
          window.location.reload();
        })
        .catch(function () {
          window.location.reload();
        });
    });
  }

  document.querySelectorAll(".finance-inline-edit").forEach(function (button) {
    button.addEventListener("click", function () {
      const field = button.dataset.field;
      const currentValue = button.dataset.value || button.textContent.trim();
      const row = button.closest(".finance-row");

      if (field === "libelle") {
        const input = document.createElement("input");
        input.type = "text";
        input.value = currentValue;
        input.className = "finance-inline-input";
        input.setAttribute("maxlength", "255");
        const original = button;
        button.replaceWith(input);
        input.focus();
        input.select();

        const save = function () {
          const nextValue = input.value.trim();
          if (nextValue.length === 0) {
            restoreButton(input, original);
            return;
          }
          const replacement = document.createElement("button");
          replacement.type = "button";
          replacement.className = "finance-inline-edit";
          replacement.dataset.field = "libelle";
          replacement.dataset.recordId = original.dataset.recordId;
          replacement.dataset.value = nextValue;
          replacement.textContent = nextValue;
          replacement.addEventListener("click", function () {
            original.click();
          });
          input.replaceWith(replacement);
          submitInlineEdit(replacement, "libelle", nextValue);
        };

        input.addEventListener("keydown", function (event) {
          if (event.key === "Enter") {
            save();
          }
          if (event.key === "Escape") {
            restoreButton(input, original);
          }
        });

        input.addEventListener("blur", function () {
          save();
        });
        return;
      }

      if (field === "category") {
        const select = document.createElement("select");
        select.className = "finance-inline-select";
        select.innerHTML =
          '<option value="">Sans catégorie</option>' +
          buildCategoryOptions(button.dataset.categoryId || "");
        const original = button;
        button.replaceWith(select);
        select.focus();

        const save = function () {
          const nextValue = select.value;
          const replacement = document.createElement("button");
          replacement.type = "button";
          replacement.className =
            "finance-inline-edit finance-category-inline-edit";
          replacement.dataset.field = "category";
          replacement.dataset.recordId = original.dataset.recordId;
          replacement.dataset.categoryId = nextValue;
          const selectedCategory = categories.find(function (category) {
            return String(category.id) === String(nextValue);
          });
          replacement.dataset.categoryName = selectedCategory
            ? selectedCategory.name
            : "";
          replacement.innerHTML = selectedCategory
            ? '<span class="finance-category-badge" style="background-color: ' +
              selectedCategory.color +
              ';">' +
              selectedCategory.name +
              "</span>"
            : '<span class="finance-category-badge" style="background-color: #999;">Sans catégorie</span>';
          select.replaceWith(replacement);
          submitInlineEdit(replacement, "category", nextValue);
        };

        select.addEventListener("change", save);
        select.addEventListener("keydown", function (event) {
          if (event.key === "Escape") {
            restoreButton(select, original);
          }
        });
      }
    });
  });

  const filterForm = document.querySelector(".finance-all-toolbar");
  if (filterForm) {
    const searchInput = document.getElementById("search");
    if (searchInput) {
      let timer;
      searchInput.addEventListener("input", function () {
        clearTimeout(timer);
        timer = setTimeout(function () {
          filterForm.submit();
        }, 350);
      });
    }

    ["year", "month", "category", "date_sort", "per_page"].forEach(
      function (fieldId) {
        const field = document.getElementById(fieldId);
        if (field) {
          field.addEventListener("change", function () {
            filterForm.submit();
          });
        }
      },
    );
  }
});
