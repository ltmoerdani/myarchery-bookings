/**
 * Initiate S2 With Dynamic Option Creation
 */
// function initiateSelect2DynamicOptionCreation(
//   elId,
//   url,
//   minimumInputLength = 3,
//   placeholder = "Masukan Pilihan",
//   attrs,
//   onSelect,
//   paramsCallback = null,
//   parentModal = null,
//   multiple = false
// ) {
//   return $(elId)
//     .select2({
//       ajax: {
//         url: url,
//         data:
//           paramsCallback ??
//           function (params) {
//             let req = {
//               q: params.term,
//             };
//             return req;
//           },
//         processResults: function (data) {
//           return {
//             results: data,
//           };
//         },
//       },
//       width: "resolve",
//       minimumInputLength: minimumInputLength,
//       dropdownParent: parentModal,
//       placeholder: placeholder,
//       multiple: multiple,
//       templateResult: function (data) {
//         var optText = "";
//         if (data.loading) {
//           return "Mencari...";
//         }

//         attrs.forEach((item, i) => {
//           let label = item.split(".");
//           let obj = data;
//           if (i !== 0) {
//             optText += " - ";
//           }
//           label.forEach((text) => {
//             obj = obj[text];
//           });
//           optText += obj;
//         });
//         return optText === undefined ? data.text : optText;
//       },
//       templateSelection: function (data) {
//         var optText = "";
//         if (!data.text) {
//           attrs.forEach((item, i) => {
//             let label = item.split(".");
//             let obj = data;
//             if (i !== 0) {
//               optText += " - ";
//             }
//             label.forEach((text) => {
//               obj = obj[text];
//             });
//             optText += obj;
//           });
//           return optText;
//         } else {
//           return data.text;
//         }
//       },
//       tags: true,
//       createTag: function (params) {
//         var term = $.trim(params.term);
//         if (term === "") {
//           return null;
//         }
//         return {
//           id: term,
//           text: term,
//           newTag: true,
//         };
//       },
//       insertTag: function (data, tag) {
//         // Insert the tag at the end of the results
//         data.push(tag);
//       },
//     })
//     .on("select2:select", function (e) {
//       const selectedData = e.params.data;

//       // Menangani tag baru yang dipilih
//       if (selectedData.newTag) {
//         console.info("new tag:", selectedData);
//       }

//       // Menjalankan callback jika disediakan
//       if (onSelect) onSelect(e);
//     });
// }
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

        let newData = {
          id: term,
          text: term,
          newTag: true,
        };

        // Tambahkan atribut yang diperlukan berdasarkan `attrs`
        attrs.forEach((attr) => {
          newData[attr] = term;
        });

        return newData;
      },
      insertTag: function (data, tag) {
        // Masukkan tag di akhir hasil
        data.push(tag);
      },
    })
    .on("select2:select", function (e) {
      const selectedData = e.params.data;
      console.log("Event select2:select terpicu dengan data:", selectedData);

      // Menjalankan callback jika disediakan
      if (onSelect) onSelect(e);
    });
}

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
        // for (let i = 0; i < attrs.length; i++) {

        //     let label = data[attrs[i]].split('.');
        //     let obj = data[attrs[i]];
        //     if (i != 0) {
        //         optText += ' - ';
        //     }
        //     label.forEach(text => {
        //         obj = obj[text];
        //     });
        //     optText += obj;
        //     console.log(optText);

        //     // text += data[attrs[i]]

        //     // if (i != attrs.length - 1) {
        //     //     text += " - "
        //     // }
        // }
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
