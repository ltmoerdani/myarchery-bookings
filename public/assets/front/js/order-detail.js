delete window.dataIndividu;
delete window.dataTeam;
delete window.dataMixTeam;
delete window.dataOfficial;
window.dataIndividu = [];
window.dataTeam = [];
window.dataMixTeam = [];
window.dataOfficial = [];

// function automatic create list city with select2 search
const createS2ListCity = (
  contentId,
  defaultValue = "",
  countryId = "",
  stateId = ""
) => {
  initiateS2(
    contentId,
    `${base_url}/api/s2-get-city${countryId ? "/" + countryId : ""}${
      stateId ? "/" + stateId : ""
    }`,
    0,
    cityDistrictProfileDefaultOption,
    ["name"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;

      window[contentFor][e.target.dataset.idx].city = e.params.data.name;
      window[contentFor][e.target.dataset.idx].city_id = e.params.data.id;
    },
    function (param) {
      let req = {
        q: param.term,
      };
      return req;
    },
    null,
    null,
    false
  );

  if (defaultValue) {
    $(contentId).select2("trigger", "select", {
      data: defaultValue,
    });
  }
};

// function automatic create list country with select2 search
const createS2ListCountry = (contentId, defaultValue = "") => {
  initiateS2(
    contentId,
    `${base_url}/api/s2-get-country`,
    0,
    countryProfileDefaultOption,
    ["name"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;

      $(`#profile_city_${e.target.dataset.contentfor}${e.target.dataset.idx}`)
        .empty()
        .trigger("change");

      window[contentFor][e.target.dataset.idx].country = e.params.data.name;
      window[contentFor][e.target.dataset.idx].country_name =
        e.params.data.name;
      window[contentFor][e.target.dataset.idx].county_id = e.params.data.id;

      createS2ListCity(
        `#profile_city_${e.target.dataset.contentfor}${e.target.dataset.idx}`,
        "",
        e.params.data.id
      );
    },
    function (param) {
      let req = {
        q: param.term,
      };
      return req;
    },
    null,
    null,
    false
  );

  if (defaultValue) {
    $(contentId).select2("trigger", "select", {
      data: defaultValue,
    });
  }
};

const handlerMapSelect2Individu = (data) => {
  for (let i = 0; i < data.length; i++) {
    console.log("data:", window.dataIndividu[i]);
    initiateSelect2DynamicOptionCreation(
      `#name_individu${data[i]}`,
      `${base_url}/customer/list-participants`,
      0,
      fullNamePlaceholder,
      ["text"],
      function (e) {
        let gender = "M";

        if (e?.params?.data?.gender) {
          if (
            ["m", "male", "pria"].includes(e.params.data.gender.toLowerCase())
          ) {
            gender = "M";
          }

          if (
            ["f", "female", "wanita"].includes(
              e.params.data.gender.toLowerCase()
            )
          ) {
            gender = "F";
          }
        }

        window.dataIndividu[e.target.dataset.idx].user_id = e.params.data.id;
        window.dataIndividu[e.target.dataset.idx].city = e.params.data.city;
        window.dataIndividu[e.target.dataset.idx].city_id =
          e.params.data.city_id;
        window.dataIndividu[e.target.dataset.idx].user_full_name =
          e.params.data.text;
        window.dataIndividu[e.target.dataset.idx].country =
          e.params.data.country_name;
        window.dataIndividu[e.target.dataset.idx].country_name =
          e.params.data.country_name;
        window.dataIndividu[e.target.dataset.idx].county_id =
          e.params.data.county_id;
        window.dataIndividu[e.target.dataset.idx].user_gender = gender;
        window.dataIndividu[e.target.dataset.idx].birthdate =
          e.params.data.birthdate;

        $(`#gender_individu${e.target.dataset.idx}`).val(gender);
        $(`#birth_date_individu${e.target.dataset.idx}`).val(
          e.params.data.birthdate
        );
        $(`#profile_country_individu${e.target.dataset.idx}`)
          .empty()
          .trigger("change");
        $(`#profile_city_individu${e.target.dataset.idx}`)
          .empty()
          .trigger("change");

        createS2ListCity(
          `#profile_city_individu${e.target.dataset.idx}`,
          "",
          ""
        );

        if (e.params.data.county_id) {
          $(`#profile_country_individu${e.target.dataset.idx}`).select2(
            "trigger",
            "select",
            {
              data: {
                id: e.params.data.county_id,
                text: e.params.data.country_name,
              },
            }
          );

          createS2ListCity(
            `#profile_city_individu${e.target.dataset.idx}`,
            e.params.data.city_id
              ? {
                  id: e.params.data.city_id,
                  text: e.params.data.city,
                }
              : "",
            e.params.data.county_id
          );
        }
      },
      function (param) {
        let req = {
          q: param.term,
        };
        return req;
      },
      null,
      null,
      false
    );

    if (window.dataIndividu[i].user_id) {
      $(`#name_individu${data[i]}`).select2("trigger", "select", {
        data: {
          id: window.dataIndividu[i].user_id,
          text: window.dataIndividu[i].user_full_name,
        },
      });
    }

    createS2ListCountry(
      `#profile_country_individu${i}`,
      window.dataIndividu[i].county_id
        ? {
            id: window.dataIndividu[i].county_id,
            text: window.dataIndividu[i].country,
          }
        : ""
    );

    createS2ListCity(
      `#profile_city_individu${i}`,
      window.dataIndividu[i].city_id
        ? {
            id: window.dataIndividu[i].city_id,
            text: window.dataIndividu[i].city_name,
          }
        : "",
      !window.dataIndividu[i]?.county_id ? "" : window.dataIndividu[i].county_id
    );
  }
};

const handlerMapSelect2Official = (data) => {
  for (let i = 0; i < data.length; i++) {
    console.log("data:", window.dataOfficial[i]);
    initiateSelect2DynamicOptionCreation(
      `#name_official${data[i]}`,
      `${base_url}/customer/list-participants`,
      0,
      fullNamePlaceholder,
      ["text"],
      function (e) {
        let gender = "M";

        if (e?.params?.data?.gender) {
          if (
            ["m", "male", "pria"].includes(e.params.data.gender.toLowerCase())
          ) {
            gender = "M";
          }

          if (
            ["f", "female", "wanita"].includes(
              e.params.data.gender.toLowerCase()
            )
          ) {
            gender = "F";
          }
        }

        window.dataOfficial[e.target.dataset.idx].user_id = e.params.data.id;
        window.dataOfficial[e.target.dataset.idx].city = e.params.data.city;
        window.dataOfficial[e.target.dataset.idx].city_id =
          e.params.data.city_id;
        window.dataOfficial[e.target.dataset.idx].user_full_name =
          e.params.data.text;
        window.dataOfficial[e.target.dataset.idx].country =
          e.params.data.country_name;
        window.dataOfficial[e.target.dataset.idx].country_name =
          e.params.data.country_name;
        window.dataOfficial[e.target.dataset.idx].county_id =
          e.params.data.county_id;
        window.dataOfficial[e.target.dataset.idx].user_gender = gender;
        window.dataOfficial[e.target.dataset.idx].birthdate =
          e.params.data.birthdate;

        $(`#gender_official${e.target.dataset.idx}`).val(gender);
        $(`#birth_date_official${e.target.dataset.idx}`).val(
          e.params.data.birthdate
        );
        $(`#profile_country_official${e.target.dataset.idx}`)
          .empty()
          .trigger("change");
        $(`#profile_city_official${e.target.dataset.idx}`)
          .empty()
          .trigger("change");

        createS2ListCity(
          `#profile_city_official${e.target.dataset.idx}`,
          "",
          ""
        );

        if (e.params.data.county_id) {
          $(`#profile_country_official${e.target.dataset.idx}`).select2(
            "trigger",
            "select",
            {
              data: {
                id: e.params.data.county_id,
                text: e.params.data.country_name,
              },
            }
          );

          createS2ListCity(
            `#profile_city_official${e.target.dataset.idx}`,
            e.params.data.city_id
              ? {
                  id: e.params.data.city_id,
                  text: e.params.data.city,
                }
              : "",
            e.params.data.county_id
          );
        }
      },
      function (param) {
        let req = {
          q: param.term,
        };
        return req;
      },
      null,
      null,
      false
    );

    if (window.dataOfficial[i].user_id) {
      $(`#name_official${data[i]}`).select2("trigger", "select", {
        data: {
          id: window.dataOfficial[i].user_id,
          text: window.dataOfficial[i].user_full_name,
        },
      });
    }

    createS2ListCountry(
      `#profile_country_official${i}`,
      window.dataOfficial[i].county_id
        ? {
            id: window.dataOfficial[i].county_id,
            text: window.dataOfficial[i].country,
          }
        : ""
    );

    createS2ListCity(
      `#profile_city_official${i}`,
      window.dataOfficial[i].city_id
        ? {
            id: window.dataOfficial[i].city_id,
            text: window.dataOfficial[i].city_name,
          }
        : "",
      !window.dataOfficial[i]?.county_id ? "" : window.dataOfficial[i].county_id
    );
  }
};

const handlerChooseGender = (contentFor, index) => {
  const mapDtWindow = `data${capitalizeFirstLetter(contentFor)}`;

  const valueGender = $(`#gender_${contentFor}${index}`).val();

  window[mapDtWindow][index].user_gender = valueGender;
};

const handlerBirthDate = (contentFor, index) => {
  const valBirthDate = $(`#birth_date_${contentFor}${index}`).val();
  const mapDtWindow = `data${capitalizeFirstLetter(contentFor)}`;

  window[mapDtWindow][index].birthdate = valBirthDate;
};

const generateViewIndividu = () => {
  const dtIndividu = window.dataIndividu;

  if (dtIndividu.length < 1) {
    return $("#individu_section").addClass("d-none");
  }

  $("#individu_section").removeClass("d-none");

  let content = ``;
  const arrIndexIndividu = [];

  dtIndividu?.map((val, index) => {
    let gender = "M";

    if (val.user_gender) {
      if (["m", "male", "pria"].includes(val.user_gender.toLowerCase())) {
        gender = "M";
      }

      if (["f", "female", "wanita"].includes(val.user_gender.toLowerCase())) {
        gender = "F";
      }
    }

    let contentDelegation = () => {};

    arrIndexIndividu.push(index);
    content += `
        <div class="card">
          <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
              id="participant_individu${index}"
              data-toggle="collapse"
              data-target="#collapse_participant_${index}"
              aria-expanded="false"
              aria-controls="collapse_participant_${index}">
              ${titleIndividuAccordion} ${index + 1}
          </div>
          <div id="collapse_participant_${index}"
              class='collapse ${index == 0 ? "show" : ""}'
              aria-labelledby="participant_individu${index}"
              data-parent="#accordionParticipant">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 form-group">
                      <label for="name_individu${index}">
                          ${fullNameLabel}*
                      </label>
                      <select data-idx="${index}" data-contentFor="individu" name="name_individu[]"
                      id="name_individu${index}" style="width:100%" class="form-control w-100">
                      </select>
                  </div>
                  <div class="col-12 col-lg-6 form-group">
                      <label
                          for="gender_individu${index}">
                          ${genderLabel}*
                      </label>
                      <select class="form-select" id="gender_individu${index}" data-idx="${index}"
                      data-contentFor="individu" name="gender_individu[]" value="${gender}"
                      onchange="handlerChooseGender('individu',${index})">
                          <option value="M">
                            ${genderMaleLang}
                          </option>
                          <option value="F">
                            ${genderFemaleLang}
                          </option>
                      </select>
                  </div>
                  <div class="col-12 col-lg-6 form-group">
                      <label
                          for="birth_date_individu${index}">
                          ${birthDateLabel}*
                      </label>
                      <input type="date" class="form-control" data-contentFor="individu"
                          id="birth_date_individu${index}"
                          name="birth_date_individu[]" data-idx="${index}"
                          placeholder="${birthDatePlaceholder}"
                          value="${!val.birthdate ? "" : val.birthdate}"
                          onchange="handlerBirthDate('individu',${index})"
                          max="${
                            new Date().toISOString().split("T")[0]
                          }" required>
                  </div>
                  <div
                      class="col-12 col-lg-6 form-group d-flex flex-column gap-2
                      content-profile-country-individu-${index}">
                      <label
                          for="profile_country_individu${index}">
                          ${countryProfileLabel}*
                      </label>
                      <select
                          class="form-select" data-contentFor="individu" id="profile_country_individu${index}"
                          name="profile_country_individu[]" data-idx="${index}" required>
                      </select>
                  </div>
                  <div
                      class="col-12 col-lg-6 d-flex flex-column gap-2 form-group
                      content-profile-city-individu-${index}">
                      <label for="profile_city_individu${index}">
                          ${cityDistrictProfileLabel}*
                      </label>
                      <select
                          data-idx="${index}"
                          data-contentFor="individu"
                          class="form-select"
                          id="profile_city_individu${index}"
                          name="profile_city_individu[]" required>
                      </select>
                  </div>

                </div>
              </div>
          </div>
        </div>
      `;
  });

  $("#form_individu").append(`
    <div class="accordion" id="accordionParticipant">
      ${content}
    </div>
  `);

  handlerMapSelect2Individu(arrIndexIndividu);
};

const generateViewTeam = () => {
  const dtTeam = window.dataTeam;

  if (dtTeam.length < 1) {
    return $("#team_section").addClass("d-none");
  }

  $("#team_section").removeClass("d-none");

  dtTeam?.map((val, index) => {
    // console.log("valTeam:", val);
    // console.log("indexTeam:", index);
  });
};

const generateViewMixTeam = () => {
  const dtMixTeam = window.dataMixTeam;

  if (dtMixTeam.length < 1) {
    return $("#mix_team_section").addClass("d-none");
  }

  $("#mix_team_section").removeClass("d-none");

  dtMixTeam?.map((val, index) => {
    // console.log("valMix:", val);
    // console.log("indexMix:", index);
  });
};

const generateViewOfficial = () => {
  const dtOfficial = window.dataOfficial;

  if (dtOfficial.length < 1) {
    return $("#official_section").addClass("d-none");
  }

  $("#official_section").removeClass("d-none");

  let contentOfficial = "";
  const arrIndexOfficial = [];

  dtOfficial?.map((val, index) => {
    console.log("valOfficial:", val);
    console.log("indexOfficial:", index);

    let gender = "M";

    if (val.user_gender) {
      if (["m", "male", "pria"].includes(val.user_gender.toLowerCase())) {
        gender = "M";
      }

      if (["f", "female", "wanita"].includes(val.user_gender.toLowerCase())) {
        gender = "F";
      }
    }
    arrIndexOfficial.push(index);
    contentOfficial += `
        <div class="card">
          <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
              id="participant_official${index}"
              data-toggle="collapse"
              data-target="#collapse_official_${index}"
              aria-expanded="false"
              aria-controls="collapse_official_${index}">
              ${titleOfficialAccordion} ${index + 1}
          </div>
          <div id="collapse_official_${index}"
              class='collapse ${index == 0 ? "show" : ""}'
              aria-labelledby="participant_official${index}"
              data-parent="#accordionParticipantOfficial">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 form-group">
                      <label for="official_name${index}">
                          ${fullNameLabel}*
                      </label>
                      <select data-contentFor="official" name="name_official[]" id="name_official${index}" data-idx="${index}" style="width:100%" class="form-control w-100"></select>
                  </div>
                  <div class="col-12 col-lg-6 form-group">
                      <label
                          for="gender_official${index}">
                          ${genderLabel}*
                      </label>
                      <select class="form-select" id="gender_official${index}" data-idx="${index}"
                      data-contentFor="official" name="gender_official[]" value="${gender}"
                      onchange="handlerChooseGender('official',${index})">
                          <option value="M">
                            ${genderMaleLang}
                          </option>
                          <option value="F">
                            ${genderFemaleLang}
                          </option>
                      </select>
                  </div>
                  <div class="col-12 col-lg-6 form-group">
                      <label
                          for="birth_date_official${index}">
                          ${birthDateLabel}*
                      </label>
                      <input type="date" class="form-control" data-contentFor="official"
                          id="birth_date_official${index}"
                          name="birth_date_official[]" data-idx="${index}"
                          placeholder="${birthDatePlaceholder}"
                          value="${!val.birthdate ? "" : val.birthdate}"
                          onchange="handlerBirthDate('official',${index})"
                          max="${
                            new Date().toISOString().split("T")[0]
                          }" required>
                  </div>
                  <div
                      class="col-12 col-lg-6 form-group d-flex flex-column gap-2
                      content-profile-country-official-${index}">
                      <label
                          for="profile_country_official${index}">
                          ${countryProfileLabel}*
                      </label>
                      <select
                          class="form-select" data-contentFor="official" id="profile_country_official${index}"
                          name="profile_country_official[]" data-idx="${index}" required>
                      </select>
                  </div>
                  <div
                      class="col-12 col-lg-6 d-flex flex-column gap-2 form-group
                      content-profile-city-official-${index}">
                      <label
                          for="profile_city_official${index}">
                          ${cityDistrictProfileLabel}*
                      </label>
                      <select
                          data-idx="${index}"
                          data-contentFor="official"
                          class="form-select"
                          id="profile_city_official${index}"
                          name="profile_city_official[]" required>
                      </select>
                  </div>
                </div>
              </div>
          </div>
        </div>
      `;
  });

  var tag_id = document.getElementById("form_official");
  tag_id.innerHTML = `
    <div class="accordion" id="accordionParticipantOfficial">
      ${contentOfficial}
    </div>
  `;
  // $("#form_official").empty();
  // $("#form_official").append(`
  //   <div class="accordion" id="accordionParticipantOfficial">
  //     ${content}
  //   </div>
  // `);

  console.log("arrIndexOfficial", arrIndexOfficial);
  // setTimeout(myGreeting, 5000);
  setTimeout(handlerMapSelect2Official(arrIndexOfficial), 5000);
};

const handlerBack = (slug, event_id) => {
  window.location.replace(`${base_url}/event/${slug}/${event_id}`);
};

const getInfoData = async () => {
  fetch(`${base_url}/get-data-form-order-tournament?checkoutID=${checkoutID}`, {
    method: "GET",
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((response) => {
      console.log("response:", response);
      const { data } = response;
      window.dataIndividu = data.ticket_detail_individu_order;
      window.dataTeam = data.ticket_detail_team_order;
      window.dataMixTeam = data.ticket_detail_mix_team_order;
      window.dataOfficial = data.ticket_detail_official_order;
      generateViewIndividu();
      generateViewTeam();
      generateViewMixTeam();
      generateViewOfficial();
    })
    .catch((error) => {
      console.log("err:", error);
      console.log("error:", error.message);
    });
};
getInfoData();

// $(document).ready(function () {
// getInfoData();
// $.ajax({
//   url: `${base_url}/get-data-form-order-tournament?checkoutID=${checkoutID}`,
//   method: "GET",
//   contentType: "JSON",
//   cache: false,
//   success: function (response) {
//     const { data } = response;
//     console.log("response:", response);
//     window.dataIndividu = data.ticket_detail_individu_order;
//     window.dataTeam = data.ticket_detail_team_order;
//     window.dataMixTeam = data.ticket_detail_mix_team_order;
//     window.dataOfficial = data.ticket_detail_official_order;
//     generateViewIndividu();
//     generateViewTeam();
//     generateViewMixTeam();
//     generateViewOfficial();
//   },
//   error: function (error) {
//     console.log("error:", error.responseJson);
//   },
// });
// });
