/**
 * Inisialisasi Select2 dengan Opsi Dinamis
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
      templateResult: function (data) {
        if (data.loading) {
          return "Mencari...";
        }

        var optText = "";

        // Tangani opsi baru
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

        // Tangani opsi baru
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
      tags: true,
      createTag: function (params) {
        var term = $.trim(params.term);
        if (term === "") {
          return null;
        }
        // Jangan buat tag baru hingga input selesai (misalnya saat menekan Enter)
        return {
          id: term,
          text: term,
          newTag: true
        };
      },
      insertTag: function (data, tag) {
        // Pastikan tag hanya di-insert sekali, setelah input selesai
        if (!data.some(existingTag => existingTag.id === tag.id)) {
          data.push(tag);
        }
      },
    })
    .on("select2:select", function (e) {
      const selectedData = e.params.data;
      console.log("Data yang dipilih:", selectedData);

      // Menjalankan callback jika disediakan
      if (onSelect) onSelect(e);

      // Memicu event change untuk memastikan Select2 mengenali pilihan baru
      $(elId).trigger('change.select2');  // Memaksa Select2 mengupdate
    })
    .on("select2:close", function (e) {
      const selectedData = $(elId).select2('data');
      if (selectedData.length > 0 && selectedData[0].newTag) {
        console.log("Tag baru dibuat:", selectedData[0]);
      }
    });
}

/**
 * Fungsi Validasi untuk Select2
 */
function validateSelect2Input(elId) {
  const selectedData = $(elId).select2('data');
  
  // Periksa apakah ada data yang dipilih
  if (selectedData.length === 0) {
    // Tidak ada data yang dipilih, input tidak valid
    return false;
  }

  // Ambil data pilihan pertama (untuk kasus single select)
  const selectedOption = selectedData[0];

  // Jika opsi adalah tag baru yang dibuat oleh pengguna
  if (selectedOption.newTag) {
    // Anggap valid karena pengguna telah membuat opsi baru
    return true;
  }

  // Jika bukan tag baru, lakukan validasi sesuai kriteria Anda
  // Misalnya, periksa apakah opsi memiliki ID atau atribut tertentu
  if (isValidExistingOption(selectedOption)) {
    return true;
  } else {
    return false;
  }
}

// Contoh fungsi untuk validasi opsi yang sudah ada
function isValidExistingOption(option) {
  // Misalnya, periksa apakah opsi memiliki properti 'id' yang valid
  return option.id !== undefined && option.id !== null && option.id !== '';
}

// Inisialisasi Validasi dengan jQuery Validation
$('#yourFormId').validate({
  rules: {
    select2FieldName: {
      required: true,
      validateSelect2: true  // Nama metode validasi kustom
    }
  },
  messages: {
    select2FieldName: {
      required: 'Field ini wajib diisi.'
    }
  }
});

// Tambahkan metode validasi kustom
$.validator.addMethod('validateSelect2', function(value, element) {
  return validateSelect2Input(element);
}, 'Silakan pilih opsi yang valid.');

// Inisialisasi Select2 di halaman
$(document).ready(function () {
  initiateSelect2DynamicOptionCreation(
    '#yourSelect2ElementId',  // Ganti dengan ID elemen Select2 Anda
    'api/url',  // Ganti dengan URL API Anda
    3,  // Minimum input length
    'Pilih opsi',  // Placeholder
    ['text'],  // Atribut untuk ditampilkan
    function (e) {
      // Callback setelah opsi dipilih
      console.log("Opsi dipilih:", e.params.data);
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
