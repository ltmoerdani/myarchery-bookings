delete window.dataIndividu;
delete window.dataTeam;
delete window.dataMix;
delete window.dataOfficial;
window.dataIndividu = [];
window.dataTeam = [];
window.dataMix = [];
window.dataOfficial = [];
window.eventInfo = [];

// automatic create list ticket with select2 search
const createS2TicketsDelegation = (
  contentIdx,
  contentFor,
  defaultValue = ""
) => {
  const dtWindow =
    window[`data${capitalizeFirstLetter(contentFor)}`][contentIdx];

  initiateS2(
    `#category_${contentFor}${contentIdx}`,
    `${base_url}/customer/s2-list-tickets/${
      window.eventInfo.event_id
    }/${contentFor}?gender=${
      !dtWindow.user_gender ? "" : dtWindow.user_gender
    }`,
    0,
    placeholderCategory,
    ["title"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;

      const checkTicket = window[contentFor].filter(
        (val) => val.ticket_id === e.params.data.id
      );

      if (checkTicket.length >= e.params.data.ticket_available) {
        $(
          `#category_${e.target.dataset.contentfor}${e.target.dataset.idx}`
        ).select2("trigger", "select", {
          data: { id: "", title: "" },
        });

        return toastr["warning"](
          `${alertOverOrderQuotaTicket} ${e.params.data.title}`
        );
      } else {
        window[contentFor][e.target.dataset.idx].ticket_id = e.params.data.id;
        window[contentFor][e.target.dataset.idx].ticket_name =
          e.params.data.category_name;
        window[contentFor][e.target.dataset.idx].sub_category_ticket_id =
          e.params.data.sub_category_id;
        window[contentFor][e.target.dataset.idx].sub_category_ticket =
          e.params.data.title;
        window[contentFor][e.target.dataset.idx].price_scheme =
          e.params.data.price_scheme;
        window[contentFor][e.target.dataset.idx].price = e.params.data.price;
        window[contentFor][e.target.dataset.idx].f_price =
          e.params.data.f_price;
        window[contentFor][e.target.dataset.idx].international_price =
          e.params.data.international_price;
        window[contentFor][e.target.dataset.idx].f_international_price =
          e.params.data.f_international_price;

        // early bird
        window[contentFor][e.target.dataset.idx].early_bird_discount =
          e.params.data.early_bird_discount;
        window[contentFor][e.target.dataset.idx].early_bird_discount_amount =
          e.params.data.early_bird_discount_amount;
        window[contentFor][
          e.target.dataset.idx
        ].early_bird_discount_amount_international =
          e.params.data.early_bird_discount_amount_international;
        window[contentFor][
          e.target.dataset.idx
        ].early_bird_discount_international_type =
          e.params.data.early_bird_discount_international_type;
        window[contentFor][
          e.target.dataset.idx
        ].early_bird_discount_international_date =
          e.params.data.early_bird_discount_international_date;
        window[contentFor][
          e.target.dataset.idx
        ].early_bird_discount_international_time =
          e.params.data.early_bird_discount_international_time;
        window[contentFor][
          e.target.dataset.idx
        ].early_bird_discount_international_end_date =
          e.params.data.early_bird_discount_international_end_date;
        window[contentFor][
          e.target.dataset.idx
        ].early_bird_discount_international_end_time =
          e.params.data.early_bird_discount_international_end_time;
        window[contentFor][e.target.dataset.idx].early_bird_discount_type =
          e.params.data.early_bird_discount_type;
        window[contentFor][e.target.dataset.idx].early_bird_discount_date =
          e.params.data.early_bird_discount_date;
        window[contentFor][e.target.dataset.idx].early_bird_discount_time =
          e.params.data.early_bird_discount_time;
        window[contentFor][e.target.dataset.idx].early_bird_discount_end_date =
          e.params.data.early_bird_discount_end_date;
        window[contentFor][e.target.dataset.idx].early_bird_discount_end_time =
          e.params.data.early_bird_discount_end_time;

        // late price
        window[contentFor][e.target.dataset.idx].late_price_discount =
          e.params.data.late_price_discount;
        window[contentFor][e.target.dataset.idx].late_price_discount_amount =
          e.params.data.late_price_discount_amount;
        window[contentFor][
          e.target.dataset.idx
        ].late_price_discount_amount_international =
          e.params.data.late_price_discount_amount_international;
        window[contentFor][
          e.target.dataset.idx
        ].late_price_discount_international_type =
          e.params.data.late_price_discount_international_type;
        window[contentFor][
          e.target.dataset.idx
        ].late_price_discount_international_date =
          e.params.data.late_price_discount_international_date;
        window[contentFor][
          e.target.dataset.idx
        ].late_price_discount_international_time =
          e.params.data.late_price_discount_international_time;
        window[contentFor][
          e.target.dataset.idx
        ].late_price_discount_international_end_date =
          e.params.data.late_price_discount_international_end_date;
        window[contentFor][
          e.target.dataset.idx
        ].late_price_discount_international_end_time =
          e.params.data.late_price_discount_international_end_time;
        window[contentFor][e.target.dataset.idx].late_price_discount_type =
          e.params.data.late_price_discount_type;
        window[contentFor][e.target.dataset.idx].late_price_discount_date =
          e.params.data.late_price_discount_date;
        window[contentFor][e.target.dataset.idx].late_price_discount_time =
          e.params.data.late_price_discount_time;
        window[contentFor][e.target.dataset.idx].late_price_discount_end_date =
          e.params.data.late_price_discount_end_date;
        window[contentFor][e.target.dataset.idx].late_price_discount_end_time =
          e.params.data.late_price_discount_end_time;
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

  if (defaultValue) {
    $(`#category_${contentFor}${contentIdx}`).select2("trigger", "select", {
      data: defaultValue,
    });
  }
};

// automatic create list city for delegation with select2 search
const createS2CityDelegation = (
  contentFor,
  contentIdx,
  defaultValue = "",
  countryId = "",
  stateId = ""
) => {
  $(`.content-delegation-city-${contentFor}-${contentIdx}`).empty();
  $(`.content-delegation-city-${contentFor}-${contentIdx}`).removeClass(
    "d-none"
  );

  $(`.content-delegation-city-${contentFor}-${contentIdx}`).append(`
    <div class="form-group d-flex flex-column gap-2">
      <label
        for="delegation_city_${contentFor}${contentIdx}">
        ${labelDelegationCity}*
      </label>
      <select
          class="form-select" data-contentFor="${contentFor}" id="delegation_city_${contentFor}${contentIdx}"
          name="delegation_city_${contentFor}[]" data-idx="${contentIdx}" required>
      </select>
    </div>
  `);

  initiateS2(
    `#delegation_city_${contentFor}${contentIdx}`,
    `${base_url}/api/s2-get-city/${countryId}/${stateId}`,
    0,
    cityDistrictProfileDefaultOption,
    ["name"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;

      window[contentFor][e.target.dataset.idx].city_delegation =
        e.params.data.id;
      window[contentFor][e.target.dataset.idx].city_delegation_name =
        e.params.data.name;
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
    $(`#delegation_city_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

// automatic create list province for delegation with select2 search
const createS2ProvinceDelegation = (
  contentFor,
  contentIdx,
  defaultValue = "",
  countryId = ""
) => {
  $(`.content-delegation-province-${contentFor}-${contentIdx}`).empty();
  $(`.content-delegation-province-${contentFor}-${contentIdx}`).removeClass(
    "d-none"
  );

  $(`.content-delegation-province-${contentFor}-${contentIdx}`).append(`
    <div class="form-group d-flex flex-column gap-2">
      <label
        for="delegation_province_${contentFor}${contentIdx}">
        ${labelDelegationProvince}*
      </label>
      <select
          class="form-select" data-contentFor="${contentFor}" id="delegation_province_${contentFor}${contentIdx}"
          name="delegation_province_${contentFor}[]" data-idx="${contentIdx}" required>
      </select>
    </div>
  `);

  initiateS2(
    `#delegation_province_${contentFor}${contentIdx}`,
    `${base_url}/api/s2-get-province/${countryId}`,
    0,
    placeholderProvince,
    ["name"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;
      window[contentFor][e.target.dataset.idx].province_delegation =
        e.params.data.id;
      window[contentFor][e.target.dataset.idx].province_delegation_name =
        e.params.data.name;
      window[contentFor][e.target.dataset.idx].city_delegation = null;
      window[contentFor][e.target.dataset.idx].city_delegation_name = null;

      $(
        `.content-delegation-city-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");
      $(
        `.content-delegation-city-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();

      if (
        ["city/district"].includes(
          window[contentFor][e.target.dataset.idx].delegation_type.toLowerCase()
        )
      ) {
        setTimeout(
          createS2CityDelegation(
            e.target.dataset.contentfor,
            e.target.dataset.idx,
            "",
            countryId,
            e.params.data.id
          ),
          300
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

  if (defaultValue) {
    $(`#delegation_province_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

// automatic create list country for delegation with select2 search
const createS2CountryDelegation = (
  contentIdx,
  contentFor,
  defaultValue = ""
) => {
  $(`.content-delegation-country-${contentFor}-${contentIdx}`).empty();
  $(`.content-delegation-country-${contentFor}-${contentIdx}`).removeClass(
    "d-none"
  );

  $(`.content-delegation-country-${contentFor}-${contentIdx}`).append(`
    <div class="form-group d-flex flex-column gap-2">
      <label
        for="delegation_country_${contentFor}${contentIdx}">
        ${labelDelegationCountry}*
      </label>
      <select
          class="form-select" data-contentFor="${contentFor}" id="delegation_country_${contentFor}${contentIdx}"
          name="delegation_country_${contentFor}[]" data-idx="${contentIdx}" required>
      </select>
    </div>
  `);

  initiateS2(
    `#delegation_country_${contentFor}${contentIdx}`,
    `${base_url}/api/s2-get-country`,
    0,
    countryProfileDefaultOption,
    ["name"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;
      window[contentFor][e.target.dataset.idx].country_delegation_name =
        e.params.data.name;
      window[contentFor][e.target.dataset.idx].country_delegation =
        e.params.data.id;
      window[contentFor][e.target.dataset.idx].province_delegation = null;
      window[contentFor][e.target.dataset.idx].province_delegation_name = null;
      window[contentFor][e.target.dataset.idx].city_delegation = null;
      window[contentFor][e.target.dataset.idx].city_delegation_name = null;
      $(
        `.content-delegation-province-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");
      $(
        `.content-delegation-city-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");
      $(
        `.content-delegation-province-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();
      $(
        `.content-delegation-city-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();

      if (
        ["city/district", "province"].includes(
          window[contentFor][e.target.dataset.idx].delegation_type.toLowerCase()
        )
      ) {
        setTimeout(
          createS2ProvinceDelegation(
            e.target.dataset.contentfor,
            e.target.dataset.idx,
            "",
            e.params.data.id
          ),
          300
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

  if (defaultValue) {
    $(`#delegation_country_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

// automatic create list school or university for delegation with select2 search
const createS2SchoolUniversityDelegation = (
  contentIdx,
  contentFor,
  defaultValue = ""
) => {
  $(`.content-delegation-school-${contentFor}-${contentIdx}`).empty();
  $(`.content-delegation-school-${contentFor}-${contentIdx}`).removeClass(
    "d-none"
  );
  $(`.content-delegation-school-${contentFor}-${contentIdx}`).append(`
    <div class="form-group d-flex flex-column gap-2">
      <label
        for="delegation_school_${contentFor}${contentIdx}">
        ${labelDelegationSchool}*
      </label>
      <select
          class="form-select" data-contentFor="${contentFor}" id="delegation_school_${contentFor}${contentIdx}"
          name="delegation_school_${contentFor}[]" data-idx="${contentIdx}" required>
      </select>
    </div>
  `);

  initiateSelect2DynamicOptionCreation(
    `#delegation_school_${contentFor}${contentIdx}`,
    `${base_url}/customer/s2-list-school-and-university`,
    0,
    placeholderSchool,
    ["text"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;

      window[contentFor][e.target.dataset.idx].school_id = e.params.data.id;
      window[contentFor][e.target.dataset.idx].school_name = e.params.data.text;
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
    $(`#delegation_school_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

// automatic create list organization for delegation with select2 search
const createS2OrganizationDelegation = (
  contentIdx,
  contentFor,
  defaultValue = ""
) => {
  $(`.content-delegation-organization-${contentFor}-${contentIdx}`).empty();
  $(`.content-delegation-organization-${contentFor}-${contentIdx}`).removeClass(
    "d-none"
  );
  $(`.content-delegation-organization-${contentFor}-${contentIdx}`).append(`
    <div class="form-group d-flex flex-column gap-2">
      <label
        for="delegation_organization_${contentFor}${contentIdx}">
        ${labelDelegationOrganization}*
      </label>
      <select
          class="form-select" data-contentFor="${contentFor}" id="delegation_organization_${contentFor}${contentIdx}"
          name="delegation_organization_${contentFor}[]" data-idx="${contentIdx}" required>
      </select>
    </div>
  `);

  initiateSelect2DynamicOptionCreation(
    `#delegation_organization_${contentFor}${contentIdx}`,
    `${base_url}/customer/s2-list-organization`,
    0,
    placeholderOrganization,
    ["text"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;

      window[contentFor][e.target.dataset.idx].organization_id =
        e.params.data.id;
      window[contentFor][e.target.dataset.idx].organization_name =
        e.params.data.text;
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
    $(`#delegation_organization_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

// automatic create list club for delegation with select2 search
const createS2ClubDelegation = (contentIdx, contentFor, defaultValue = "") => {
  $(`.content-delegation-club-${contentFor}-${contentIdx}`).empty();
  $(`.content-delegation-club-${contentFor}-${contentIdx}`).removeClass(
    "d-none"
  );
  $(`.content-delegation-club-${contentFor}-${contentIdx}`).append(`
    <div class="form-group d-flex flex-column gap-2">
      <label
        for="delegation_club_${contentFor}${contentIdx}">
        ${labelDelegationClub}*
      </label>
      <select
          class="form-select" data-contentFor="${contentFor}" id="delegation_club_${contentFor}${contentIdx}"
          name="delegation_club_${contentFor}[]" data-idx="${contentIdx}" required>
      </select>
    </div>
  `);

  initiateSelect2DynamicOptionCreation(
    `#delegation_club_${contentFor}${contentIdx}`,
    `${base_url}/customer/s2-list-clubs`,
    0,
    placeholderClub,
    ["text"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;

      window[contentFor][e.target.dataset.idx].club_id = e.params.data.id;
      window[contentFor][e.target.dataset.idx].club_name = e.params.data.text;
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
    $(`#delegation_club_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

// function automatic create list delegation with select2 search
const createS2ListDelegation = (contentId, defaultValue = "") => {
  console.log("defaultValue:", defaultValue);
  initiateS2(
    contentId,
    `${base_url}/api/s2-get-delegation-type`,
    0,
    labelDelegationType,
    ["name"],
    function (e) {
      const contentFor = `data${capitalizeFirstLetter(
        e.target.dataset.contentfor
      )}`;

      window[contentFor][e.target.dataset.idx].delegation_type =
        e.params.data.name;

      // hide all content delegation
      $(
        `.content-delegation-country-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");
      $(
        `.content-delegation-province-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");
      $(
        `.content-delegation-city-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");
      $(
        `.content-delegation-school-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");
      $(
        `.content-delegation-organization-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");
      $(
        `.content-delegation-club-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).addClass("d-none");

      //empty all content delegation
      $(
        `.content-delegation-country-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();
      $(
        `.content-delegation-province-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();
      $(
        `.content-delegation-city-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();
      $(
        `.content-delegation-school-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();
      $(
        `.content-delegation-organization-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();
      $(
        `.content-delegation-club-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
      ).empty();
      console.log("test:", e.params.data);

      switch (e?.params?.data?.name?.toLowerCase()) {
        case "club":
          createS2ClubDelegation(
            e.target.dataset.idx,
            e.target.dataset.contentfor
          );
          break;
        case "school/universities":
          createS2SchoolUniversityDelegation(
            e.target.dataset.idx,
            e.target.dataset.contentfor
          );
          break;
        case "organization":
          createS2OrganizationDelegation(
            e.target.dataset.idx,
            e.target.dataset.contentfor
          );
          break;
        default:
          createS2CountryDelegation(
            e.target.dataset.idx,
            e.target.dataset.contentfor
          );
          break;
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

  if (defaultValue) {
    $(contentId).select2("trigger", "select", {
      data: defaultValue,
    });
  }
};

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
    if ($(`#name_individu${data[i]}`)) {
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
            e.params.data.country;
          window.dataIndividu[e.target.dataset.idx].country_name =
            e.params.data.country;
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
                  text: e.params.data.country,
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

          setTimeout(() => {
            createS2TicketsDelegation(e.target.dataset.idx, "individu");
          }, 300);
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
    }

    if (window.dataIndividu[i].user_id) {
      $(`#name_individu${data[i]}`).select2("trigger", "select", {
        data: {
          id: window.dataIndividu[i].user_id,
          text: window.dataIndividu[i].user_full_name,
        },
      });
    }

    if ($(`#profile_country_individu${i}`)) {
      createS2ListCountry(
        `#profile_country_individu${i}`,
        window.dataIndividu[i].county_id
          ? {
              id: window.dataIndividu[i].county_id,
              text: window.dataIndividu[i].country,
            }
          : ""
      );
    }

    if (`#profile_city_individu${i}`) {
      createS2ListCity(
        `#profile_city_individu${i}`,
        window.dataIndividu[i].city_id
          ? {
              id: window.dataIndividu[i].city_id,
              text: window.dataIndividu[i].city_name,
            }
          : "",
        !window.dataIndividu[i]?.county_id
          ? ""
          : window.dataIndividu[i].county_id
      );
    }

    if ($(`#delegation_individu${i}`)) {
      createS2ListDelegation(
        `#delegation_individu${i}`,
        window.dataIndividu[i].delegation_type
          ? {
              id: window.dataIndividu[i].delegation_type,
              text: window.dataIndividu[i].delegation_type,
            }
          : ""
      );
    }

    // hide all content delegation
    $(`.content-delegation-country-individu-${i}`).addClass("d-none");
    $(`.content-delegation-province-individu-${i}`).addClass("d-none");
    $(`.content-delegation-city-individu-${i}`).addClass("d-none");
    $(`.content-delegation-school-individu-${i}`).addClass("d-none");
    $(`.content-delegation-organization-individu-${i}`).addClass("d-none");
    $(`.content-delegation-club-individu-${i}`).addClass("d-none");

    // empty all content delegation
    $(`.content-delegation-country-individu-${i}`).empty();
    $(`.content-delegation-province-individu-${i}`).empty();
    $(`.content-delegation-city-individu-${i}`).empty();
    $(`.content-delegation-school-individu-${i}`).empty();
    $(`.content-delegation-organization-individu-${i}`).empty();
    $(`.content-delegation-club-individu-${i}`).empty();

    setTimeout(() => {
      if (window.dataIndividu[i].contingent_type.toLowerCase() !== "open") {
        switch (window.dataIndividu[i].delegation_type.toLowerCase()) {
          case "country":
            createS2CountryDelegation(i, "individu");
            break;
          case "province":
            createS2ProvinceDelegation(
              "individu",
              i,
              "",
              window.dataIndividu[i].country_delegation
            );
            break;
          case "city/district":
            createS2CityDelegation(
              "individu",
              i,
              "",
              window.dataIndividu[i].country_delegation,
              window.dataIndividu[i].province_delegation
            );
            break;
          case "school/universities":
            createS2SchoolUniversityDelegation(i, "individu");
            break;
          case "organization":
            createS2OrganizationDelegation(i, "individu");
            break;
          default:
            createS2ClubDelegation(i, "individu");
            break;
        }
      }
    }, 500);

    createS2TicketsDelegation(i, "individu");
  }
};

const handlerMapSelect2Official = (data) => {
  for (let i = 0; i < data.length; i++) {
    if ($(`#name_official${data[i]}`)) {
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
            e.params.data.country;
          window.dataOfficial[e.target.dataset.idx].country_name =
            e.params.data.country;
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
                  text: e.params.data.country,
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
    }

    if (window.dataOfficial[i].user_id) {
      $(`#name_official${data[i]}`).select2("trigger", "select", {
        data: {
          id: window.dataOfficial[i].user_id,
          text: window.dataOfficial[i].user_full_name,
        },
      });
    }

    if ($(`#profile_country_official${i}`)) {
      createS2ListCountry(
        `#profile_country_official${i}`,
        window.dataOfficial[i].county_id
          ? {
              id: window.dataOfficial[i].county_id,
              text: window.dataOfficial[i].country,
            }
          : ""
      );
    }

    if ($(`#profile_city_official${i}`)) {
      createS2ListCity(
        `#profile_city_official${i}`,
        window.dataOfficial[i].city_id
          ? {
              id: window.dataOfficial[i].city_id,
              text: window.dataOfficial[i].city_name,
            }
          : "",
        !window.dataOfficial[i]?.county_id
          ? ""
          : window.dataOfficial[i].county_id
      );
    }

    if ($(`#delegation_official${i}`)) {
      createS2ListDelegation(
        `#delegation_official${i}`,
        window.dataOfficial[i].county_id
          ? {
              id: window.dataOfficial[i].county_id,
              text: window.dataOfficial[i].country,
            }
          : ""
      );
    }

    // hide all content delegation
    $(`.content-delegation-country-official-${i}`).addClass("d-none");
    $(`.content-delegation-province-official-${i}`).addClass("d-none");
    $(`.content-delegation-city-official-${i}`).addClass("d-none");
    $(`.content-delegation-school-official-${i}`).addClass("d-none");
    $(`.content-delegation-organization-official-${i}`).addClass("d-none");
    $(`.content-delegation-club-official-${i}`).addClass("d-none");

    // empty all content delegation
    $(`.content-delegation-country-official-${i}`).empty();
    $(`.content-delegation-province-official-${i}`).empty();
    $(`.content-delegation-city-official-${i}`).empty();
    $(`.content-delegation-school-official-${i}`).empty();
    $(`.content-delegation-organization-official-${i}`).empty();
    $(`.content-delegation-club-official-${i}`).empty();
    setTimeout(() => {
      if (window.dataOfficial[i].contingent_type.toLowerCase() !== "open") {
        switch (window.dataOfficial[i].delegation_type.toLowerCase()) {
          case "country":
            createS2CountryDelegation(i, "official");
            break;
          case "province":
            createS2ProvinceDelegation(
              "official",
              i,
              "",
              window.dataOfficial[i].country_delegation
            );
            break;
          case "city/district":
            createS2CityDelegation(
              "official",
              i,
              "",
              window.dataOfficial[i].country_delegation,
              window.dataOfficial[i].province_delegation
            );
            break;
          case "school/universities":
            createS2SchoolUniversityDelegation(i, "official");
            break;
          case "organization":
            createS2OrganizationDelegation(i, "official");
            break;
          default:
            createS2ClubDelegation(i, "official");
            break;
        }
      }
    }, 500);
  }
};

const handlerMapSelect2Team = (data) => {
  for (let i = 0; i < data.length; i++) {
    if ($(`#delegation_team${i}`)) {
      createS2ListDelegation(
        `#delegation_team${i}`,
        window.dataTeam[i].delegation_type
          ? {
              id: window.dataTeam[i].delegation_type,
              text: window.dataTeam[i].delegation_type,
            }
          : ""
      );
    }

    // hide all content delegation
    $(`.content-delegation-country-team-${i}`).addClass("d-none");
    $(`.content-delegation-province-team-${i}`).addClass("d-none");
    $(`.content-delegation-city-team-${i}`).addClass("d-none");
    $(`.content-delegation-school-team-${i}`).addClass("d-none");
    $(`.content-delegation-organization-team-${i}`).addClass("d-none");
    $(`.content-delegation-club-team-${i}`).addClass("d-none");

    // empty all content delegation
    $(`.content-delegation-country-team-${i}`).empty();
    $(`.content-delegation-province-team-${i}`).empty();
    $(`.content-delegation-city-team-${i}`).empty();
    $(`.content-delegation-school-team-${i}`).empty();
    $(`.content-delegation-organization-team-${i}`).empty();
    $(`.content-delegation-club-team-${i}`).empty();

    setTimeout(() => {
      if (window.dataTeam[i].contingent_type.toLowerCase() !== "open") {
        switch (window.dataTeam[i].delegation_type.toLowerCase()) {
          case "country":
            createS2CountryDelegation(i, "team");
            break;
          case "province":
            createS2ProvinceDelegation(
              "team",
              i,
              "",
              window.dataTeam[i].country_delegation
            );
            break;
          case "city/district":
            createS2CityDelegation(
              "team",
              i,
              "",
              window.dataTeam[i].country_delegation,
              window.dataTeam[i].province_delegation
            );
            break;
          case "school/universities":
            createS2SchoolUniversityDelegation(i, "team");
            break;
          case "organization":
            createS2OrganizationDelegation(i, "team");
            break;
          default:
            createS2ClubDelegation(i, "team");
            break;
        }
      }
    }, 500);

    createS2TicketsDelegation(i, "team");
  }
};

const handlerMapSelect2MixTeam = (data) => {
  for (let i = 0; i < data.length; i++) {
    if ($(`#delegation_mix${i}`)) {
      createS2ListDelegation(
        `#delegation_mix${i}`,
        window.dataMix[i].delegation_type
          ? {
              id: window.dataMix[i].delegation_type,
              text: window.dataMix[i].delegation_type,
            }
          : ""
      );
    }

    // hide all content delegation
    $(`.content-delegation-country-mix-${i}`).addClass("d-none");
    $(`.content-delegation-province-mix-${i}`).addClass("d-none");
    $(`.content-delegation-city-mix-${i}`).addClass("d-none");
    $(`.content-delegation-school-mix-${i}`).addClass("d-none");
    $(`.content-delegation-organization-mix-${i}`).addClass("d-none");
    $(`.content-delegation-club-mix-${i}`).addClass("d-none");

    // empty all content delegation
    $(`.content-delegation-country-mix-${i}`).empty();
    $(`.content-delegation-province-mix-${i}`).empty();
    $(`.content-delegation-city-mix-${i}`).empty();
    $(`.content-delegation-school-mix-${i}`).empty();
    $(`.content-delegation-organization-mix-${i}`).empty();
    $(`.content-delegation-club-mix-${i}`).empty();

    setTimeout(() => {
      if (window.dataMix[i].contingent_type.toLowerCase() !== "open") {
        switch (window.dataMix[i].delegation_type.toLowerCase()) {
          case "country":
            createS2CountryDelegation(i, "mix");
            break;
          case "province":
            createS2ProvinceDelegation(
              "mix",
              i,
              "",
              window.dataMix[i].country_delegation
            );
            break;
          case "city/district":
            createS2CityDelegation(
              "mix",
              i,
              "",
              window.dataMix[i].country_delegation,
              window.dataMix[i].province_delegation
            );
            break;
          case "school/universities":
            createS2SchoolUniversityDelegation(i, "mix");
            break;
          case "organization":
            createS2OrganizationDelegation(i, "mix");
            break;
          default:
            createS2ClubDelegation(i, "mix");
            break;
        }
      }
    }, 500);

    createS2TicketsDelegation(i, "mix");
  }
};

const handlerChooseGender = (contentFor, index) => {
  const mapDtWindow = `data${capitalizeFirstLetter(contentFor)}`;

  const valueGender = $(`#gender_${contentFor}${index}`).val();
  window[mapDtWindow][index].user_gender = valueGender;
  setTimeout(() => {
    createS2TicketsDelegation(index, contentFor);
  }, 300);
};

const handlerBirthDate = (contentFor, index) => {
  const valBirthDate = $(`#birth_date_${contentFor}${index}`).val();
  const mapDtWindow = `data${capitalizeFirstLetter(contentFor)}`;

  window[mapDtWindow][index].birthdate = valBirthDate;
};

const handlerNameTeam = (contentFor, index) => {
  const valInput = $(`#name_${contentFor}${index}`).val();
  const mapDtWindow = `data${capitalizeFirstLetter(contentFor)}`;
  window[mapDtWindow][index].team_name = valInput;
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
    console.log("val:", val);
    let gender = "M";

    if (val.user_gender) {
      if (["m", "male", "pria"].includes(val.user_gender.toLowerCase())) {
        gender = "M";
      }

      if (["f", "female", "wanita"].includes(val.user_gender.toLowerCase())) {
        gender = "F";
      }
    }

    let contentDelegation = "";

    if (val.contingent_type.toLowerCase() === "open") {
      contentDelegation += `
        <div class="col-12 col-md-6 form-group d-flex flex-column gap-2
          content-delegation-individu-${index}">
            <label
                for="delegation_individu${index}">
                ${labelDelegationType}*
            </label>
            <select
                class="form-select" data-contentFor="individu" id="delegation_individu${index}"
                name="delegation_individu[]" data-idx="${index}" required>
            </select>
        </div>
      `;
    }

    if (val.contingent_type.toLowerCase() !== "open") {
      contentDelegation = `
        <input type="hidden"name="delegation_individu[]"  value="${val.delegation_type}" />
      `;
    }

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
                  <div class="col-12 col-md-6 form-group">
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
                  <div class="col-12 col-md-6 form-group">
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
                      class="col-12 col-md-6 form-group d-flex flex-column gap-2
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
                      class="col-12 col-md-6 d-flex flex-column gap-2 form-group
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
                  ${contentDelegation}
                  <div class="content-delegation-country-individu-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-province-individu-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-city-individu-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-school-individu-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-club-individu-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-organization-individu-${index} col-12 col-md-6 d-none"></div>
                  <div class="col-12 col-lg-6 d-flex flex-column gap-2 form-group">
                    <label for="category_individu${index}">
                          ${labelCategory}
                    </label>
                    <select
                        data-idx="${index}"
                        data-contentFor="individu"
                        class="form-select"
                        id="category_individu${index}"
                        name="category_individu[]" required>
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

  let content = ``;
  const arrIndexTeam = [];

  dtTeam?.map((val, index) => {
    let contentDelegation = "";

    if (val.contingent_type.toLowerCase() === "open") {
      contentDelegation += `
        <div class="col-12 col-lg-6 form-group d-flex flex-column gap-2
          content-delegation-team-${index}">
            <label
                for="delegation_team${index}">
                ${labelDelegationType}*
            </label>
            <select
                class="form-select" data-contentFor="team" id="delegation_team${index}"
                name="delegation_team[]" data-idx="${index}" required>
            </select>
        </div>
      `;
    }

    if (val.contingent_type.toLowerCase() !== "open") {
      contentDelegation = `
        <input type="hidden"name="delegation_team[]"  value="${val.delegation_type}" />
      `;
    }

    arrIndexTeam.push(index);
    content += `
      <div class="card">
        <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer" id="participant_team${index}" data-toggle="collapse" aria-expanded="false" data-target="#collapse_participant_team_${index}" aria-controls="collapse_participant_team_${index}">
          ${titleTeamAccordion} ${index + 1}
        </div>
        <div id="collapse_participant_team_${index}"
        class='collapse ${
          index == 0 ? "show" : ""
        }' aria-labelledby="collapse_participant_team_${index}" data-parent="#accordionParticipantTeam">
          <div class="card-body">
            <div class="row">
              <div class="col-12 form-group">
                  <label for="name_team${index}">
                      ${labelTeamName}*
                  </label>
                  <input type="text" class="form-control" data-contentFor="team" id="name_team${index}"
                  name="name_team[]" data-idx="${index}" placeholder="${placeholderTeamName}"
                  value="${
                    !val.team_name ? "" : val.team_name
                  }" onchange="handlerNameTeam('team',${index})" required>
              </div>
              ${contentDelegation}
              <div class="content-delegation-country-team-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-province-team-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-city-team-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-school-team-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-club-team-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-organization-team-${index} col-12 col-md-6 d-none"></div>
              <div class="col-12 col-lg-6">
                <label for="category_team${index}">
                      ${labelCategory}
                </label>
                <select
                    data-idx="${index}"
                    data-contentFor="team"
                    class="form-select"
                    id="category_team${index}"
                    name="category_team[]" required>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  $("#form_team").append(`
    <div class="accordion" id="accordionParticipantTeam">
      ${content}
    </div>
  `);

  handlerMapSelect2Team(arrIndexTeam);
};

const generateViewMixTeam = () => {
  const dtMixTeam = window.dataMix;
  if (dtMixTeam.length < 1) {
    return $("#mix_team_section").addClass("d-none");
  }

  $("#mix_team_section").removeClass("d-none");

  let content = ``;
  const arrIndexMixTeam = [];

  dtMixTeam?.map((val, index) => {
    let contentDelegation = "";

    if (val.contingent_type.toLowerCase() === "open") {
      contentDelegation += `
        <div class="col-12 col-lg-6 form-group d-flex flex-column gap-2
          content-delegation-mix-${index}">
            <label
                for="delegation_mix${index}">
                ${labelDelegationType}*
            </label>
            <select
                class="form-select" data-contentFor="mix" id="delegation_mix${index}"
                name="delegation_mix[]" data-idx="${index}" required>
            </select>
        </div>
      `;
    }

    if (val.contingent_type.toLowerCase() !== "open") {
      contentDelegation = `
        <input type="hidden" name="delegation_mix[]" value="${val.delegation_type}" />
      `;
    }

    arrIndexMixTeam.push(index);

    content += `
      <div class="card">
        <div class="card-header bg-primary-1 text-white text-left collapsed cursor-pointer"
        id="participant_mix${index}" data-toggle="collapse" aria-expanded="false"
        data-target="#collapse_participant_mix_${index}" aria-controls="collapse_participant_mix_${index}">
          ${titleMixTeamAccordion} ${index + 1}
        </div>
        <div id="collapse_participant_mix_${index}"
        class='collapse ${
          index == 0 ? "show" : ""
        }' aria-labelledby="collapse_participant_mix_${index}" data-parent="#accordionParticipantMix">
          <div class="card-body">
            <div class="row">
              <div class="col-12 form-group">
                  <label for="name_mix${index}">
                      ${labelTeamName}*
                  </label>
                  <input type="text" class="form-control" data-contentFor="mix" id="name_mix${index}"
                  name="name_mix[]" data-idx="${index}" placeholder="${placeholderTeamName}"
                  value="${
                    !val.team_name ? "" : val.team_name
                  }" onchange="handlerNameTeam('mix',${index})" required>
              </div>
              ${contentDelegation}
              <div class="content-delegation-country-mix-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-province-mix-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-city-mix-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-school-mix-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-club-mix-${index} col-12 col-md-6 d-none"></div>
              <div class="content-delegation-organization-mix-${index} col-12 col-md-6 d-none"></div>
              <div class="col-12 col-lg-6">
                <label for="category_mix${index}">
                      ${labelCategory}
                </label>
                <select
                    data-idx="${index}"
                    data-contentFor="mix"
                    class="form-select"
                    id="category_mix${index}"
                    name="category_mix[]" required>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  });

  $("#form_mix_team").append(`
    <div class="accordion" id="accordionParticipantMix">
      ${content}
    </div>
  `);

  handlerMapSelect2MixTeam(arrIndexMixTeam);
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
    let gender = "M";

    if (val.user_gender) {
      if (["m", "male", "pria"].includes(val.user_gender.toLowerCase())) {
        gender = "M";
      }

      if (["f", "female", "wanita"].includes(val.user_gender.toLowerCase())) {
        gender = "F";
      }
    }

    let contentDelegation = "";

    if (val.contingent_type.toLowerCase() === "open") {
      contentDelegation = `
        <div class="col-12 col-md-6 form-group d-flex flex-column gap-2
          content-delegation-official-${index}">
            <label
                for="delegation_official${index}">
                ${labelDelegationType}*
            </label>
            <select
                class="form-select" data-contentFor="official" id="delegation_official${index}"
                name="delegation_official[]" data-idx="${index}" required>
            </select>
        </div>
      `;
    }

    if (val.contingent_type.toLowerCase() !== "open") {
      contentDelegation = `
      <input type="hidden"name="delegation_official[]"  value="${val.delegation_type}" />
      `;
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
                  ${contentDelegation}
                  <div class="content-delegation-country-official-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-province-official-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-city-official-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-school-official-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-club-official-${index} col-12 col-md-6 d-none"></div>
                  <div class="content-delegation-organization-official-${index} col-12 col-md-6 d-none"></div>
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

  setTimeout(handlerMapSelect2Official(arrIndexOfficial), 5000);
};

const generateSummaryTickets = () => {
  let totalTickets = 0;
  let contentListTickets = "";

  if (window.dataIndividu.length > 0) {
    totalTickets = totalTickets + window.dataIndividu.length;
    contentListTickets += `
      <div class="d-flex justify-content-between">
        <p class="font-weight-medium mb-0">
          Individu
        </p>
        <p class="font-weight-medium mb-0">
          ${window.dataIndividu.length}
        </p>
      </div>
      <div class="mt-0">
          <hr style="width:100%;text-align:left;margin-left:0">
      </div>
    `;
  }

  if (window.dataTeam.length > 0) {
    totalTickets = totalTickets + window.dataTeam.length;
    contentListTickets += `
      <div class="d-flex justify-content-between">
        <p class="font-weight-medium mb-0">
          Team
        </p>
        <p class="font-weight-medium mb-0">
          ${window.dataTeam.length}
        </p>
      </div>
      <div class="mt-0">
          <hr style="width:100%;text-align:left;margin-left:0">
      </div>
    `;
  }

  if (window.dataMix.length > 0) {
    totalTickets = totalTickets + window.dataMix.length;
    contentListTickets += `
      <div class="d-flex justify-content-between">
        <p class="font-weight-medium mb-0">
          Mix Team
        </p>
        <p class="font-weight-medium mb-0">
          ${window.dataMix.length}
        </p>
      </div>
      <div class="mt-0">
          <hr style="width:100%;text-align:left;margin-left:0">
      </div>
    `;
  }

  if (window.dataOfficial.length > 0) {
    totalTickets = totalTickets + window.dataOfficial.length;
    contentListTickets += `
      <div class="d-flex justify-content-between">
        <p class="font-weight-medium mb-0">
          Official
        </p>
        <p class="font-weight-medium mb-0">
          ${window.dataOfficial.length}
        </p>
      </div>
      <div class="mt-0">
          <hr style="width:100%;text-align:left;margin-left:0">
      </div>
    `;
  }

  $(".list-category-tickets").append(contentListTickets);
  $(".total-tickets").empty();
  $(".total-tickets").append(totalTickets);
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
      const { data } = response;
      window.dataIndividu = data.ticket_detail_individu_order;
      console.log("window.dataIndividu:", window.dataIndividu);
      window.dataTeam = data.ticket_detail_team_order;
      window.dataMix = data.ticket_detail_mix_team_order;
      window.dataOfficial = data.ticket_detail_official_order;
      window.eventInfo = data.event;

      generateViewIndividu();
      generateViewTeam();
      generateViewMixTeam();
      generateViewOfficial();
      setTimeout(() => {
        generateSummaryTickets();
        $("#eventErrors ul").empty();
        $("#eventErrors").hide();
      }, 300);
    })
    .catch((error) => {
      console.log("err:", error);
      console.log("error:", error.message);
    });
};
getInfoData();

$("#ToDetailCheckout").on("click", function (e) {
  // $(e.target).attr("disabled", true);
  // $(".request-loader").addClass("show");
  $("#eventErrors ul").empty();
  $("#eventErrors").hide();
  let bookingForm = document.getElementById("bookingForm");
  let fd = new FormData(bookingForm);

  fd.append(
    "individu",
    window.dataIndividu.length < 1 ? "" : JSON.stringify(window.dataIndividu)
  );
  fd.append(
    "team",
    window.dataTeam.length < 1 ? "" : JSON.stringify(window.dataTeam)
  );
  fd.append(
    "mix_team",
    window.dataMix.length < 1 ? "" : JSON.stringify(window.dataMix)
  );
  fd.append(
    "official",
    window.dataOfficial.length < 1 ? "" : JSON.stringify(window.dataOfficial)
  );
  fd.append("event_info", JSON.stringify(window.eventInfo));
  fd.append("checkoutID", checkoutID);

  let url = $("#bookingForm").attr("action");
  let method = $("#bookingForm").attr("method");

  $.ajax({
    url: url,
    method: method,
    data: fd,
    contentType: false,
    processData: false,
    success: function (response) {
      console.log("response:", response);
    },
    statusCode: {
      419: function (response) {
        location.reload();
      },
    },
    error: function (error) {
      let errors = ``;
      for (let x in error.responseJSON?.errors?.message) {
        errors += `<li>
                <p class="text-danger mb-0">${error.responseJSON.errors.message[x]}</p>
              </li>`;
      }

      $("#eventErrors ul").html(errors);
      $("#eventErrors").show();

      $(".request-loader").removeClass("show");

      $("html, body").animate(
        {
          scrollTop: $("#eventErrors").offset().top - 100,
        },
        1000
      );
      console.log("error:", error.responseJSON.errors);
    },
  });
});
