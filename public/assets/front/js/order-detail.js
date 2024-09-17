const AllDataForm = {
  individu: [],
  mix_team: [],
  team: [],
  official: [],
  eventInfo: {},
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
      AllDataForm.individu = data.ticket_detail_individu_order;
      AllDataForm.team = data.ticket_detail_team_order;
      AllDataForm.mix_team = data.ticket_detail_mix_team_order;
      AllDataForm.official = data.ticket_detail_official_order;
      AllDataForm.eventInfo = data.event;

      generateViewIndividu();
      // generateViewTeam();
      // generateViewMixTeam();
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

// global function
// automatic create list ticket with select2 search
const createS2TicketsDelegation = (
  contentIdx,
  contentFor,
  defaultValue = ""
) => {
  const dtSingle = AllDataForm[contentFor][contentIdx];

  initiateS2(
    `#category_${contentFor}${contentIdx}`,
    `${base_url}/customer/s2-list-tickets/${
      AllDataForm.eventInfo.event_id
    }/${contentFor}?gender=${
      !dtSingle.user_gender ? "" : dtSingle.user_gender
    }`,
    0,
    placeholderCategory,
    ["title"],
    function (e) {
      if (e.params.data.id) {
        const checkTicket = AllDataForm[contentFor].filter(
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
          AllDataForm[contentFor][e.target.dataset.idx].ticket_id =
            e.params.data.id;
          AllDataForm[contentFor][e.target.dataset.idx].ticket_name =
            e.params.data.category_name;
          AllDataForm[contentFor][e.target.dataset.idx].sub_category_ticket_id =
            e.params.data.sub_category_id;
          AllDataForm[contentFor][e.target.dataset.idx].sub_category_ticket =
            e.params.data.title;
          AllDataForm[contentFor][e.target.dataset.idx].price_scheme =
            e.params.data.price_scheme;
          AllDataForm[contentFor][e.target.dataset.idx].price =
            e.params.data.price;
          AllDataForm[contentFor][e.target.dataset.idx].f_price =
            e.params.data.f_price;
          AllDataForm[contentFor][e.target.dataset.idx].international_price =
            e.params.data.international_price;
          AllDataForm[contentFor][e.target.dataset.idx].f_international_price =
            e.params.data.f_international_price;

          // early bird
          AllDataForm[contentFor][e.target.dataset.idx].early_bird_discount =
            e.params.data.early_bird_discount;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_amount =
            e.params.data.early_bird_discount_amount;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_amount_international =
            e.params.data.early_bird_discount_amount_international;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_international_type =
            e.params.data.early_bird_discount_international_type;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_international_date =
            e.params.data.early_bird_discount_international_date;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_international_time =
            e.params.data.early_bird_discount_international_time;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_international_end_date =
            e.params.data.early_bird_discount_international_end_date;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_international_end_time =
            e.params.data.early_bird_discount_international_end_time;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_type = e.params.data.early_bird_discount_type;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_date = e.params.data.early_bird_discount_date;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_time = e.params.data.early_bird_discount_time;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_end_date =
            e.params.data.early_bird_discount_end_date;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].early_bird_discount_end_time =
            e.params.data.early_bird_discount_end_time;

          // late price
          AllDataForm[contentFor][e.target.dataset.idx].late_price_discount =
            e.params.data.late_price_discount;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_amount =
            e.params.data.late_price_discount_amount;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_amount_international =
            e.params.data.late_price_discount_amount_international;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_international_type =
            e.params.data.late_price_discount_international_type;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_international_date =
            e.params.data.late_price_discount_international_date;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_international_time =
            e.params.data.late_price_discount_international_time;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_international_end_date =
            e.params.data.late_price_discount_international_end_date;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_international_end_time =
            e.params.data.late_price_discount_international_end_time;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_type = e.params.data.late_price_discount_type;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_date = e.params.data.late_price_discount_date;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_time = e.params.data.late_price_discount_time;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_end_date =
            e.params.data.late_price_discount_end_date;
          AllDataForm[contentFor][
            e.target.dataset.idx
          ].late_price_discount_end_time =
            e.params.data.late_price_discount_end_time;
        }
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
      if (e.params.data.id) {
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].city_delegation = e.params.data.id;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].city_delegation_name = e.params.data.name;
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
    $(`#delegation_city_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

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
      if (e.params.data.id) {
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].province_delegation = e.params.data.id;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].province_delegation_name = e.params.data.name;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].city_delegation = null;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].city_delegation_name = null;

        $(
          `.content-delegation-city-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
        ).addClass("d-none");
        $(
          `.content-delegation-city-${e.target.dataset.contentfor}-${e.target.dataset.idx}`
        ).empty();

        if (
          ["city/district"].includes(
            AllDataForm[e.target.dataset.contentfor][
              e.target.dataset.idx
            ].delegation_type.toLowerCase()
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
      if (e.params.data.id) {
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].country_delegation_name = e.params.data.name;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].country_delegation = e.params.data.id;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].province_delegation = null;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].province_delegation_name = null;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].city_delegation = null;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].city_delegation_name = null;

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
            AllDataForm[e.target.dataset.contentfor][
              e.target.dataset.idx
            ].delegation_type.toLowerCase()
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
      if (e.params.data.id) {
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].school_id = e.params.data.id;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].school_name = e.params.data.text;
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
    $(`#delegation_school_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

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
      if (e.params.data.id) {
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].organization_id = e.params.data.id;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].organization_name = e.params.data.text;
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
    $(`#delegation_organization_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

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
      if (e.params.data.id) {
        AllDataForm[e.target.dataset.contentfor][e.target.dataset.idx].club_id =
          e.params.data.id;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].club_name = e.params.data.text;
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
    $(`#delegation_club_${contentFor}${contentIdx}`).select2(
      "trigger",
      "select",
      {
        data: defaultValue,
      }
    );
  }
};

const createS2ListDelegation = (contentId, defaultValue = "") => {
  initiateS2(
    contentId,
    `${base_url}/api/s2-get-delegation-type`,
    0,
    labelDelegationType,
    ["name"],
    function (e) {
      if (e.params.data.name) {
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].delegation_type = e.params.data.name;

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
      if (e.params.data.id) {
        AllDataForm[e.target.dataset.contentfor][e.target.dataset.idx].city =
          e.params.data.name;
        AllDataForm[e.target.dataset.contentfor][e.target.dataset.idx].city_id =
          e.params.data.id;
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

const createS2ListCountry = (contentId, defaultValue = "") => {
  initiateS2(
    contentId,
    `${base_url}/api/s2-get-country`,
    0,
    countryProfileDefaultOption,
    ["name"],
    function (e) {
      $(`#profile_city_${e.target.dataset.contentfor}${e.target.dataset.idx}`)
        .empty()
        .trigger("change");

      if (e.params.data.id) {
        AllDataForm[e.target.dataset.contentfor][e.target.dataset.idx].country =
          e.params.data.name;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].country_name = e.params.data.name;
        AllDataForm[e.target.dataset.contentfor][
          e.target.dataset.idx
        ].county_id = e.params.data.id;

        createS2ListCity(
          `#profile_city_${e.target.dataset.contentfor}${e.target.dataset.idx}`,
          "",
          e.params.data.id
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
    $(contentId).select2("trigger", "select", {
      data: defaultValue,
    });
  }
};

const handlerChooseGender = (contentFor, index) => {
  const valueGender = $(`#gender_${contentFor}${index}`).val();
  AllDataForm[contentFor][index].user_gender = valueGender;
  setTimeout(() => {
    createS2TicketsDelegation(index, contentFor);
  }, 300);
};

const handlerBirthDate = (contentFor, index) => {
  const valBirthDate = $(`#birth_date_${contentFor}${index}`).val();
  AllDataForm[contentFor][index].birthdate = valBirthDate;
};

const handlerNameTeam = (contentFor, index) => {
  const valInput = $(`#name_${contentFor}${index}`).val();
  AllDataForm[contentFor][index].team_name = valInput;
};

const generateSummaryTickets = () => {
  let totalTickets = 0;
  let contentListTickets = "";

  if (AllDataForm.individu.length > 0) {
    totalTickets = totalTickets + AllDataForm.individu.length;
    contentListTickets += `
      <div class="d-flex justify-content-between">
        <p class="font-weight-medium mb-0">
          Individu
        </p>
        <p class="font-weight-medium mb-0">
          ${AllDataForm.individu.length}
        </p>
      </div>
      <div class="mt-0">
          <hr style="width:100%;text-align:left;margin-left:0">
      </div>
    `;
  }

  if (AllDataForm.team.length > 0) {
    totalTickets = totalTickets + AllDataForm.team.length;
    contentListTickets += `
      <div class="d-flex justify-content-between">
        <p class="font-weight-medium mb-0">
          Team
        </p>
        <p class="font-weight-medium mb-0">
          ${AllDataForm.team.length}
        </p>
      </div>
      <div class="mt-0">
          <hr style="width:100%;text-align:left;margin-left:0">
      </div>
    `;
  }

  if (AllDataForm.mix_team.length > 0) {
    totalTickets = totalTickets + AllDataForm.mix_team.length;
    contentListTickets += `
      <div class="d-flex justify-content-between">
        <p class="font-weight-medium mb-0">
          Mix Team
        </p>
        <p class="font-weight-medium mb-0">
          ${AllDataForm.mix_team.length}
        </p>
      </div>
      <div class="mt-0">
          <hr style="width:100%;text-align:left;margin-left:0">
      </div>
    `;
  }

  if (AllDataForm.official.length > 0) {
    totalTickets = totalTickets + AllDataForm.official.length;
    contentListTickets += `
      <div class="d-flex justify-content-between">
        <p class="font-weight-medium mb-0">
          Official
        </p>
        <p class="font-weight-medium mb-0">
          ${AllDataForm.official.length}
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
  AllDataForm.appData.location.replace(`${base_url}/event/${slug}/${event_id}`);
};

// Individu Schema
const handlerMapSelect2Individu = (data) => {
  for (let i = 0; i < data.length; i++) {
    console.log("AllDataForm.individu[i]:", AllDataForm.individu[i]);
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

          if (e.params.data.id) {
            AllDataForm.individu[e.target.dataset.idx].user_id =
              e.params.data.id;

            $(`#profile_country_individu${e.target.dataset.idx}`)
              .empty()
              .trigger("change");
            $(`#profile_city_individu${e.target.dataset.idx}`)
              .empty()
              .trigger("change");
          }

          if (e.params.data.text) {
            AllDataForm.individu[e.target.dataset.idx].user_full_name =
              e.params.data.text;
          }

          if (e.params.data.country) {
            AllDataForm.individu[e.target.dataset.idx].country =
              e.params.data.country;
            AllDataForm.individu[e.target.dataset.idx].country_name =
              e.params.data.country;
          }

          if (e.params.data.county_id) {
            AllDataForm.individu[e.target.dataset.idx].county_id =
              e.params.data.county_id;

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
          }

          if (e.params.data.city) {
            AllDataForm.individu[e.target.dataset.idx].city =
              e.params.data.city;
          }

          if (e.params.data.city_id) {
            AllDataForm.individu[e.target.dataset.idx].city_id =
              e.params.data.city_id;

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

          if (e.params.data.gender) {
            AllDataForm.individu[e.target.dataset.idx].user_gender = gender;
          }

          if (e.params.data.birthdate) {
            AllDataForm.individu[e.target.dataset.idx].birthdate =
              e.params.data.birthdate;

            $(`#birth_date_individu${e.target.dataset.idx}`).val(
              e.params.data.birthdate
            );
          }

          AllDataForm.individu[e.target.dataset.idx].user_gender = gender;

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

    if (AllDataForm.individu[i].user_id) {
      $(`#name_individu${data[i]}`).select2("trigger", "select", {
        data: {
          id: AllDataForm.individu[i].user_id,
          text: AllDataForm.individu[i].user_full_name,
        },
      });
    }

    if ($(`#profile_country_individu${i}`)) {
      createS2ListCountry(
        `#profile_country_individu${i}`,
        AllDataForm.individu[i].county_id
          ? {
              id: AllDataForm.individu[i].county_id,
              text: AllDataForm.individu[i].country,
            }
          : ""
      );
    }

    if (`#profile_city_individu${i}`) {
      createS2ListCity(
        `#profile_city_individu${i}`,
        AllDataForm.individu[i].city_id
          ? {
              id: AllDataForm.individu[i].city_id,
              text: AllDataForm.individu[i].city,
            }
          : "",
        !AllDataForm.individu[i]?.county_id
          ? ""
          : AllDataForm.individu[i].county_id
      );
    }

    if ($(`#delegation_individu${i}`)) {
      createS2ListDelegation(
        `#delegation_individu${i}`,
        AllDataForm.individu[i].delegation_type
          ? {
              id: AllDataForm.individu[i].delegation_type,
              text: AllDataForm.individu[i].delegation_type,
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

    if (AllDataForm.individu[i].contingent_type.toLowerCase() !== "open") {
      switch (AllDataForm.individu[i].delegation_type.toLowerCase()) {
        case "country":
          createS2CountryDelegation(i, "individu");
          break;
        case "province":
          createS2ProvinceDelegation(
            "individu",
            i,
            "",
            AllDataForm.individu[i].country_delegation
          );
          break;
        case "city/district":
          createS2CityDelegation(
            "individu",
            i,
            "",
            AllDataForm.individu[i].country_delegation,
            AllDataForm.individu[i].province_delegation
          );
          break;
        case "school/universities":
          createS2SchoolUniversityDelegation(
            i,
            "individu",
            AllDataForm.individu[i].school_id
              ? {
                  id: AllDataForm.individu[i].school_id,
                  text: AllDataForm.individu[i].school_name,
                }
              : ""
          );
          break;
        case "organization":
          createS2OrganizationDelegation(
            i,
            "individu",
            AllDataForm.individu[i].organization_id
              ? {
                  id: AllDataForm.individu[i].organization_id,
                  text: AllDataForm.individu[i].organization_name,
                }
              : ""
          );
          break;
        default:
          createS2ClubDelegation(
            i,
            "individu",
            AllDataForm.individu[i].club_id
              ? {
                  id: AllDataForm.individu[i].club_id,
                  text: AllDataForm.individu[i].club_name,
                }
              : ""
          );
          break;
      }
    }

    if (AllDataForm.individu[i].contingent_type.toLowerCase() === "open") {
      if (AllDataForm.individu[i].delegation_type) {
        switch (AllDataForm.individu[i].delegation_type.toLowerCase()) {
          case "country":
            createS2CountryDelegation(i, "individu");
            break;
          case "province":
            createS2ProvinceDelegation(
              "individu",
              i,
              "",
              AllDataForm.individu[i].country_delegation
            );
            break;
          case "city/district":
            createS2CityDelegation(
              "individu",
              i,
              "",
              AllDataForm.individu[i].country_delegation,
              AllDataForm.individu[i].province_delegation
            );
            break;
          case "school/universities":
            createS2SchoolUniversityDelegation(
              i,
              "individu",
              AllDataForm.individu[i].school_id
                ? {
                    id: AllDataForm.individu[i].school_id,
                    text: AllDataForm.individu[i].school_name,
                  }
                : ""
            );
            break;
          case "organization":
            createS2OrganizationDelegation(
              i,
              "individu",
              AllDataForm.individu[i].organization_id
                ? {
                    id: AllDataForm.individu[i].organization_id,
                    text: AllDataForm.individu[i].organization_name,
                  }
                : ""
            );
            break;
          default:
            createS2ClubDelegation(
              i,
              "individu",
              AllDataForm.individu[i].club_id
                ? {
                    id: AllDataForm.individu[i].club_id,
                    text: AllDataForm.individu[i].club_name,
                  }
                : ""
            );
            break;
        }
      }
    }

    createS2TicketsDelegation(
      i,
      "individu",
      AllDataForm.individu[i].ticket_id
        ? {
            id: AllDataForm.individu[i].ticket_id,
            text: AllDataForm.individu[i].sub_category_ticket,
          }
        : ""
    );
  }
};

const generateViewIndividu = () => {
  if (AllDataForm.individu.length < 1) {
    return $("#individu_section").addClass("d-none");
  }

  $("#individu_section").removeClass("d-none");

  let content = ``;
  const arrIndexIndividu = [];
  AllDataForm.individu?.map((val, index) => {
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

  setTimeout(() => {
    handlerMapSelect2Individu(arrIndexIndividu);
  }, 200);
};

// Official Schema
const handlerMapSelect2Official = (data) => {
  for (let i = 0; i < data.length; i++) {
    console.log("data official:", data[i]);
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

          if (e.params.data.id) {
            AllDataForm.official[e.target.dataset.idx].user_id =
              e.params.data.id;

            $(`#profile_country_official${e.target.dataset.idx}`)
              .empty()
              .trigger("change");
            $(`#profile_city_official${e.target.dataset.idx}`)
              .empty()
              .trigger("change");
          }

          if (e.params.data.text) {
            AllDataForm.official[e.target.dataset.idx].user_full_name =
              e.params.data.text;
          }

          if (e.params.data.country) {
            AllDataForm.official[e.target.dataset.idx].country =
              e.params.data.country;
            AllDataForm.official[e.target.dataset.idx].country_name =
              e.params.data.country;
          }

          if (e.params.data.county_id) {
            AllDataForm.official[e.target.dataset.idx].county_id =
              e.params.data.county_id;

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
          }

          if (e.params.data.city) {
            AllDataForm.official[e.target.dataset.idx].city =
              e.params.data.city;
          }
          if (e.params.data.city_id) {
            AllDataForm.official[e.target.dataset.idx].city_id =
              e.params.data.city_id;

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

          if (e.params.data.gender) {
            AllDataForm.official[e.target.dataset.idx].user_gender = gender;
          }

          if (e.params.data.birthdate) {
            AllDataForm.official[e.target.dataset.idx].birthdate =
              e.params.data.birthdate;

            $(`#birth_date_official${e.target.dataset.idx}`).val(
              e.params.data.birthdate
            );
          }

          AllDataForm.official[e.target.dataset.idx].user_gender = gender;

          setTimeout(() => {
            createS2TicketsDelegation(e.target.dataset.idx, "official");
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

    if (AllDataForm.official[i].user_id) {
      $(`#name_official${data[i]}`).select2("trigger", "select", {
        data: {
          id: AllDataForm.official[i].user_id,
          text: AllDataForm.official[i].user_full_name,
        },
      });
    }

    if ($(`#profile_country_official${i}`)) {
      createS2ListCountry(
        `#profile_country_official${i}`,
        AllDataForm.official[i].county_id
          ? {
              id: AllDataForm.official[i].county_id,
              text: AllDataForm.official[i].country,
            }
          : ""
      );
    }

    if ($(`#profile_city_official${i}`)) {
      createS2ListCity(
        `#profile_city_official${i}`,
        AllDataForm.official[i].city_id
          ? {
              id: AllDataForm.official[i].city_id,
              text: AllDataForm.official[i].city,
            }
          : "",
        !AllDataForm.official[i]?.county_id
          ? ""
          : AllDataForm.official[i].county_id
      );
    }

    if ($(`#delegation_official${i}`)) {
      createS2ListDelegation(
        `#delegation_official${i}`,
        AllDataForm.official[i].delegation_type
          ? {
              id: AllDataForm.official[i].delegation_type,
              text: AllDataForm.official[i].delegation_type,
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

    if (AllDataForm.official[i].contingent_type.toLowerCase() !== "open") {
      switch (AllDataForm.official[i].delegation_type.toLowerCase()) {
        case "country":
          createS2CountryDelegation(i, "official");
          break;
        case "province":
          createS2ProvinceDelegation(
            "official",
            i,
            "",
            AllDataForm.official[i].country_delegation
          );
          break;
        case "city/district":
          createS2CityDelegation(
            "official",
            i,
            "",
            AllDataForm.official[i].country_delegation,
            AllDataForm.official[i].province_delegation
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

    console.log("AllDataForm.official[i]:", AllDataForm.official[i]);

    if (AllDataForm.official[i].contingent_type.toLowerCase() === "open") {
      if (AllDataForm.official[i].delegation_type) {
        switch (AllDataForm.official[i].delegation_type.toLowerCase()) {
          case "country":
            createS2CountryDelegation(i, "official");
            break;
          case "province":
            createS2ProvinceDelegation(
              "official",
              i,
              AllDataForm.official[i].school_id
                ? {
                    id: AllDataForm.official[i].school_id,
                    text: AllDataForm.official[i].school_name,
                  }
                : "",
              AllDataForm.official[i].country_delegation
            );
            break;
          case "city/district":
            createS2CityDelegation(
              "official",
              i,
              "",
              AllDataForm.official[i].country_delegation,
              AllDataForm.official[i].province_delegation
            );
            break;
          case "school/universities":
            createS2SchoolUniversityDelegation(
              i,
              "official",
              AllDataForm.official[i].school_id
                ? {
                    id: AllDataForm.official[i].school_id,
                    text: AllDataForm.official[i].school_name,
                  }
                : ""
            );
            break;
          case "organization":
            createS2OrganizationDelegation(
              i,
              "official",
              AllDataForm.official[i].organization_id
                ? {
                    id: AllDataForm.official[i].organization_id,
                    text: AllDataForm.official[i].organization_name,
                  }
                : ""
            );
            break;
          default:
            createS2ClubDelegation(
              i,
              "official",
              AllDataForm.official[i].club_id
                ? {
                    id: AllDataForm.official[i].club_id,
                    text: AllDataForm.official[i].club_name,
                  }
                : ""
            );
            break;
        }
      }
    }

    createS2TicketsDelegation(i, "official");
  }
};

const generateViewOfficial = () => {
  if (AllDataForm.official.length < 1) {
    return $("#official_section").addClass("d-none");
  }

  $("#official_section").removeClass("d-none");

  let contentOfficial = "";
  const arrIndexOfficial = [];

  AllDataForm.official?.map((val, index) => {
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

  setTimeout(() => {
    handlerMapSelect2Official(arrIndexOfficial);
  }, 200);
};

$("#ToDetailCheckout").on("click", function (e) {
  // $(e.target).attr("disabled", true);
  // $(".request-loader").addClass("show");
  $("#eventErrors ul").empty();
  $("#eventErrors").hide();
  let bookingForm = document.getElementById("bookingForm");
  let fd = new FormData(bookingForm);

  fd.append(
    "individu",
    AllDataForm.individu.length < 1 ? "" : JSON.stringify(AllDataForm.individu)
  );
  fd.append(
    "team",
    AllDataForm.team.length < 1 ? "" : JSON.stringify(AllDataForm.team)
  );
  fd.append(
    "mix_team",
    AllDataForm.mix_team.length < 1 ? "" : JSON.stringify(AllDataForm.mix_team)
  );
  fd.append(
    "official",
    AllDataForm.official.length < 1 ? "" : JSON.stringify(AllDataForm.official)
  );
  fd.append("event_info", JSON.stringify(AllDataForm.eventInfo));
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
