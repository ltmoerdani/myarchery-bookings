/**
 * Inisialisasi Select2 dengan Opsi Dinamis dan `selectOnClose`
 */
function initiateSelect2DynamicOptionCreation(
  elId,
  url,
  minimumInputLength = 3,
  placeholder = "Masukan Pilihan",
  attrs,
  onSelect,
  paramsCallback = null,
  parentModal = null,
  multiple = false
) {
  return $(elId)
    .select2({
      ajax: {
        url: url,
        data:
          paramsCallback ??
          function (params) {
            let req = {
              q: params.term,
            };
            return req;
          },
        processResults: function (data) {
          return {
            results: data,
          };
        },
      },
      width: "resolve",
      minimumInputLength: minimumInputLength,
      dropdownParent: parentModal,
      placeholder: placeholder,
      multiple: multiple,
      tags: true,
      selectOnClose: true, // Opsi penting ditambahkan di sini
      createTag: function (params) {
        var term = $.trim(params.term);
        if (term === "") {
          return null;
        }
        return {
          id: term,
          text: term,
          newTag: true,
        };
      },
      insertTag: function (data, tag) {
        if (!data.some((existingTag) => existingTag.id === tag.id)) {
          data.push(tag);
        }
      },
      templateResult: function (data) {
        if (data.loading) {
          return "Mencari...";
        }

        var optText = "";

        if (data.newTag) {
          optText = data.text;
        } else {
          attrs.forEach((item, i) => {
            let label = item.split(".");
            let obj = data;
            if (i !== 0) {
              optText += " - ";
            }
            label.forEach((text) => {
              obj = obj[text];
            });
            optText += obj;
          });
        }

        return optText === undefined ? data.text : optText;
      },
      templateSelection: function (data) {
        var optText = "";

        if (data.newTag) {
          optText = data.text;
        } else if (!data.text) {
          attrs.forEach((item, i) => {
            let label = item.split(".");
            let obj = data;
            if (i !== 0) {
              optText += " - ";
            }
            label.forEach((text) => {
              obj = obj[text];
            });
            optText += obj;
          });
        } else {
          optText = data.text;
        }

        return optText;
      },
    })
    .on("select2:select", function (e) {
      const selectedData = e.params.data;
      // console.log("selected:", selectedData);

      // Menjalankan callback jika disediakan
      if (onSelect) onSelect(e);
    });
  // Tidak perlu lagi event handler 'select2:close'
}

/**
 * Fungsi Validasi untuk Select2
 */
function validateSelect2Input(elId) {
  const selectedData = $(elId).select2("data");

  if (selectedData.length === 0) {
    return false;
  }

  const selectedOption = selectedData[0];

  if (selectedOption.newTag) {
    return true;
  }

  return isValidExistingOption(selectedOption);
}

// Contoh fungsi untuk validasi opsi yang sudah ada
function isValidExistingOption(option) {
  return option.id !== undefined && option.id !== null && option.id !== "";
}

// Inisialisasi Validasi dengan jQuery Validation
$("#yourFormId").validate({
  rules: {
    select2FieldName: {
      required: true,
      validateSelect2: true,
    },
  },
  messages: {
    select2FieldName: {
      required: "Field ini wajib diisi.",
    },
  },
});

// Tambahkan metode validasi kustom
$.validator.addMethod(
  "validateSelect2",
  function (value, element) {
    return validateSelect2Input(element);
  },
  "Silakan pilih opsi yang valid."
);

// Inisialisasi Select2 di halaman
$(document).ready(function () {
  initiateSelect2DynamicOptionCreation(
    "#yourSelect2ElementId", // Ganti dengan ID elemen Select2 Anda
    "api/url", // Ganti dengan URL API Anda
    3, // Minimum input length
    "Pilih opsi", // Placeholder
    ["text"], // Atribut untuk ditampilkan
    function (e) {
      // Callback setelah opsi dipilih
      // console.log("Opsi dipilih:", e.params.data);
    }
  );
});

/**
 * Initiate S2 With option
 */
function initiateS2(
  elId,
  url,
  minimumInputLength = 3,
  placeholder = "Masukan Pilihan",
  attrs,
  onSelect,
  paramsCallback = null,
  parentModal = null,
  multiple = false
) {
  return $(elId)
    .select2({
      ajax: {
        url: url,
        data:
          paramsCallback ??
          function (params) {
            let req = {
              q: params.term,
            };
            return req;
          },
        processResults: function (data) {
          return { results: data };
        },
      },
      width: "resolve",
      minimumInputLength: minimumInputLength,
      dropdownParent: parentModal,
      placeholder: placeholder,
      multiple: multiple,
      templateResult: function (data) {
        var text = "";
        var optText = "";
        if (!data.loading)
          attrs.forEach((item, i) => {
            let label = item.split(".");
            let obj = data;
            if (i != 0) {
              optText += " - ";
            }
            label.forEach((text) => {
              obj = obj[text];
            });
            optText += obj;
          });
        return data.loading ? "Mencari..." : optText;
      },
      templateSelection: function (data) {
        var text = "";
        var optText = "";
        if (!data.text)
          attrs.forEach((item, i) => {
            let label = item.split(".");
            let obj = data;
            if (i != 0) {
              optText += " - ";
            }
            label.forEach((text) => {
              obj = obj[text];
            });
            optText += obj;
          });
        // for (let i = 0; i < attrs.length; i++) {
        //     text += data[attrs[i]]

        //     if (i != attrs.length - 1) {
        //         text += " - "
        //     }
        // }
        return data.text || optText;
      },
    })
    .on("select2:select", function (e) {
      // form.downstream.country_id = e.target.value
      if (onSelect) onSelect(e);
    });
}
