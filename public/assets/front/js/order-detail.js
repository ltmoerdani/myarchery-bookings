$(document).ready(function () {
  window.dataIndividu = [];
  window.dataTeam = [];
  window.dataMixTeam = [];
  window.dataOfficial = [];

  $.ajax({
    url: `${base_url}/get-data-form-order-tournament?checkoutID=${checkoutID}`,
    method: "GET",
    contentType: false,
    success: function (response) {
      const { data } = response;
      console.log("data:", data);
      window.dataIndividu = data.ticket_detail_individu_order;
      window.dataTeam = data.ticket_detail_team_order;
      window.dataMixTeam = data.ticket_detail_mix_team_order;
      window.dataOfficial = data.ticket_detail_official_order;
      generateViewIndividu();
      generateViewTeam();
      generateViewMixTeam();
      generateViewOfficial();
    },
    error: function (error) {
      console.log("error:", error.responseJson);
    },
  });

  const generateViewIndividu = () => {
    const dtIndividu = window.dataIndividu;

    if (dtIndividu.length < 1) {
      return $("#individu_section").addClass("d-none");
    }

    $("#individu_section").removeClass("d-none");

    let content = ``;

    dtIndividu?.map((val, index) => {
      console.log("val:", val);
      console.log("index:", index);
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
                      <label for="name_individu{{ $i }}">
                          ${fullNameLabel}*
                      </label>
                      <input type="text" class="form-control" id="name_individu${index}" name="name_individu[]" placeholder="${fullNamePlaceholder}" required>
                  </div>
                  <div class="col-12 col-lg-6 form-group">
                      <label
                          for="gender_individu${index}">
                          ${genderLabel}*
                      </label>
                      <select class="form-select"
                          id="gender_individu${index}"
                          name="gender_individu[]">
                          <option value="male" selected>
                            ${genderMaleLang}
                          </option>
                          <option value="female">
                            ${genderFemaleLang}
                          </option>
                      </select>
                  </div>
                  <div class="col-12 col-lg-6 form-group">
                      <label
                          for="birth_date_individu${index}">
                          ${birthDateLabel}*
                      </label>
                      <input type="date" class="form-control"
                          id="birth_date_individu${index}"
                          name="birth_date_individu[]"
                          placeholder="${birthDatePlaceholder}"
                          max="${
                            new Date().toISOString().split("T")[0]
                          }" required>
                  </div>
                  <div
                      class="col-12 col-lg-6 form-group d-flex flex-column gap-2 content-profile-country-individu-${index}">
                      <label
                          for="profile_country_individu${index}">
                          ${countryProfileLabel}*
                      </label>
                      <select
                          class="form-select js-example-basic-single"
                          id="profile_country_individu${index}"
                          name="profile_country_individu[]"
                          onchange="handlerProfileCountry(${index})"
                          required>
                          <option value="" selected disabled>
                              ${countryProfileDefaultOption}
                          </option>
                      </select>
                  </div>
                  <div
                      class="col-12 col-lg-6 d-flex flex-column gap-2 form-group content-profile-city-individu-${index}">
                      <label
                          for="profile_city_individu${index}">
                          ${cityDistrictProfileLabel}*
                      </label>
                      <select
                          class="form-select js-example-basic-single"
                          id="profile_city_individu${index}"
                          name="profile_city_individu[]" required>
                          <option value="" selected disabled>
                              ${cityDistrictProfileDefaultOption}
                          </option>
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
  };

  const generateViewTeam = () => {
    const dtTeam = window.dataTeam;

    if (dtTeam.length < 1) {
      return $("#team_section").addClass("d-none");
    }

    $("#team_section").removeClass("d-none");

    dtTeam?.map((val, index) => {
      console.log("valTeam:", val);
      console.log("indexTeam:", index);
    });
  };

  const generateViewMixTeam = () => {
    const dtMixTeam = window.dataMixTeam;

    if (dtMixTeam.length < 1) {
      return $("#mix_team_section").addClass("d-none");
    }

    $("#mix_team_section").removeClass("d-none");

    dtMixTeam?.map((val, index) => {
      console.log("valMix:", val);
      console.log("indexMix:", index);
    });
  };

  const generateViewOfficial = () => {
    const dtOfficial = window.dataOfficial;

    if (dtOfficial.length < 1) {
      return $("#official_section").addClass("d-none");
    }

    $("#official_section").removeClass("d-none");

    let content = ``;

    dtOfficial?.map((val, index) => {
      console.log("valOfficial:", val);
      console.log("indexOfficial:", index);
      content += `
        <div class="card">
          <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
              id="participant_individu${index}"
              data-toggle="collapse"
              data-target="#collapse_official_${index}"
              aria-expanded="false"
              aria-controls="collapse_official_${index}">
              ${titleOfficialAccordion} ${index + 1}
          </div>
          <div id="collapse_official_${index}"
              class='collapse ${index == 0 ? "show" : ""}'
              aria-labelledby="participant_individu${index}"
              data-parent="#accordionParticipant">
              <div class="card-body">
                <div class="row">
                  <div class="col-12 form-group">
                      <label for="name_official{{ $i }}">
                          ${fullNameLabel}*
                      </label>
                      <input type="text" class="form-control" id="name_official${index}" name="name_official[]" placeholder="${fullNamePlaceholder}" required>
                  </div>
                  <div class="col-12 col-lg-6 form-group">
                      <label
                          for="gender_official${index}">
                          ${genderLabel}*
                      </label>
                      <select class="form-select"
                          id="gender_official${index}"
                          name="gender_official[]">
                          <option value="male" selected>
                            ${genderMaleLang}
                          </option>
                          <option value="female">
                            ${genderFemaleLang}
                          </option>
                      </select>
                  </div>
                  <div class="col-12 col-lg-6 form-group">
                      <label
                          for="birth_date_official${index}">
                          ${birthDateLabel}*
                      </label>
                      <input type="date" class="form-control"
                          id="birth_date_official${index}"
                          name="birth_date_official[]"
                          placeholder="${birthDatePlaceholder}"
                          max="${
                            new Date().toISOString().split("T")[0]
                          }" required>
                  </div>
                  <div
                      class="col-12 col-lg-6 form-group d-flex flex-column gap-2 content-profile-country-individu-${index}">
                      <label
                          for="profile_country_official${index}">
                          ${countryProfileLabel}*
                      </label>
                      <select
                          class="form-select js-example-basic-single"
                          id="profile_country_official${index}"
                          name="profile_country_official[]"
                          onchange="handlerProfileCountry(${index})"
                          required>
                          <option value="" selected disabled>
                              ${countryProfileDefaultOption}
                          </option>
                      </select>
                  </div>
                  <div
                      class="col-12 col-lg-6 d-flex flex-column gap-2 form-group content-profile-city-individu-${index}">
                      <label
                          for="profile_city_official${index}">
                          ${cityDistrictProfileLabel}*
                      </label>
                      <select
                          class="form-select js-example-basic-single"
                          id="profile_city_official${index}"
                          name="profile_city_official[]" required>
                          <option value="" selected disabled>
                              ${cityDistrictProfileDefaultOption}
                          </option>
                      </select>
                  </div>
                </div>
              </div>
          </div>
        </div>
      `;
    });

    $("#form_official").append(`
      <div class="accordion" id="accordionExampleOfficial">
        ${content}
      </div>
    `);
  };
});

const handlerBack = (slug, event_id) => {
  location.replace(`${base_url}/event/${slug}/${event_id}`);
};
