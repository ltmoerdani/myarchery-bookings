/**
 * Initiate S2 With Dynamic Option Creation
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
        var newTag = {
          id: term,
          text: term,
          newTag: true,
        };

        // Hanya return tag, jangan trigger langsung
        return newTag;
      },
      insertTag: function (data, tag) {
        // Masukkan tag di akhir hasil
        data.push(tag);

        // Tunggu hingga enter ditekan untuk memilih tag baru
        $(elId).one('select2:selecting', function () {
          $(elId).select2('trigger', 'select', {
            data: tag
          });
        });
        
        $(elId).trigger('change');
      },
    })
    .on("select2:select", function (e) {
      const selectedData = e.params.data;
      console.log("Data yang dipilih:", selectedData);

      // Menjalankan callback jika disediakan
      if (onSelect) onSelect(e);
    })
    .on("select2:close", function (e) {
      const selectedData = $(elId).select2('data');
      if (selectedData.length > 0 && selectedData[0].newTag) {
        console.log("Tag baru dibuat:", selectedData[0]);
      }
    });
}

$(document).ready(function () {
  // Contoh penggunaan fungsi initiateSelect2DynamicOptionCreation
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
