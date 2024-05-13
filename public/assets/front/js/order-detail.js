$(document).ready(function () {
  $(".js-example-basic-single").select2({
    selectionCssClass: "form-select",
  });
});
const base_url = $("#base_url").val();

const handlerDelegationIndividu = (i) => {
  const getTypeDelegationInput = document.getElementById(
    `delegation_individu${i}`
  ).value;

  $(`.content-delegation-country-individu-${i}`).addClass("d-none");
  $(`.content-delegation-country-individu-${i}`).empty();
  $(`.content-delegation-province-individu-${i}`).addClass("d-none");
  $(`.content-delegation-province-individu-${i}`).empty();
  $(`.content-delegation-city-individu-${i}`).addClass("d-none");
  $(`.content-delegation-city-individu-${i}`).empty();
  $(`.content-delegation-school-individu-${i}`).addClass("d-none");
  $(`.content-delegation-school-individu-${i}`).empty();
  $(`.content-delegation-club-individu-${i}`).addClass("d-none");
  $(`.content-delegation-club-individu-${i}`).empty();

  switch (getTypeDelegationInput.toLowerCase()) {
    case "country":
      handlerSetContentDelegationCountryIndividu(i);
      break;
    case "province":
      handlerSetContentDelegationCountryIndividu(i);
      break;
    case "city/district":
      handlerSetContentDelegationCountryIndividu(i);
      break;
    case "school/universities":
      handlerSetContentDelegationSchoolIndividu(i);
      break;
    default:
      handlerSetContentDelegationClubIndividu(i);
      break;
  }
  console.log("getTypeDelegationInput:", getTypeDelegationInput);
};

const handlerSetContentDelegationCountryIndividu = async (
  i,
  default_value = null
) => {
  const dataCountry = await getCountry();
  let countryOptions = "";
  dataCountry?.data.map((val) => {
    countryOptions += `
      <option
          value="${val.id}">
          ${val.name}
      </option>
    `;
  });

  $(`.content-delegation-country-individu-${i}`).removeClass("d-none");
  $(`.content-delegation-country-individu-${i}`).append(`
    <label
        for="country_delegation_individu${i}">
        Country*
    </label>
    <select class="form-select js-select2-country-individu-delegation"
        id="country_delegation_individu${i}"
        name="country_delegation_individu[]"
        onchange="handlerSetContentDelegationProvinceIndividu(${i})"
        value="${!default_value ? "" : default_value}"
        required>
        <option value="" selected disabled>Choose Country</option>
        ${countryOptions}
    </select>
  `);
  $(".js-select2-country-individu-delegation").select2({
    selectionCssClass: "form-select",
  });
};

const handlerSetContentDelegationProvinceIndividu = async (
  i,
  default_value = null
) => {
  const getTypeDelegationInput = document.getElementById(
    `delegation_individu${i}`
  ).value;
  if (getTypeDelegationInput.toLowerCase() !== "country") {
    const valueCountry = $(`#country_delegation_individu${i}`).val();
    const getDataProvince = await getProvince(valueCountry);
    let provinceOptions = "";
    getDataProvince?.data.map((val) => {
      provinceOptions += `
      <option
          value="${val.id}">
          ${val.name}
      </option>
    `;
    });

    $(`.content-delegation-province-individu-${i}`).removeClass("d-none");
    $(`.content-delegation-province-individu-${i}`).empty();
    $(`.content-delegation-province-individu-${i}`).append(`
      <label
          for="province_delegation_individu${i}">
          Province*
      </label>
      <select class="form-select js-select2-province-individu-delegation"
          id="province_delegation_individu${i}"
          name="province_delegation_individu[]"
          onchange="handlerSetContentDelegationCityIndividu(${i})"
          value="${!default_value ? "" : default_value}"
          required>
          <option value="" selected disabled>Choose Province</option>
          ${provinceOptions}
      </select>
    `);
    $(".js-select2-province-individu-delegation").select2({
      selectionCssClass: "form-select",
    });
  }
};

const handlerSetContentDelegationCityIndividu = async (i) => {
  const getTypeDelegationInput = document.getElementById(
    `delegation_individu${i}`
  ).value;
  if (
    !["country", "province", "state"].includes(
      getTypeDelegationInput.toLowerCase()
    )
  ) {
    const valueCountry = $(`#country_delegation_individu${i}`).val();
    const valueProvince = $(`#province_delegation_individu${i}`).val();
    const getDataCity = await getCity(120, 1);
    console.log("getDataCity:", getDataCity);
    $(`.content-delegation-city-individu-${i}`).removeClass("d-none");
  }
};

const handlerSetContentDelegationSchoolIndividu = (i) => {
  $(`.content-delegation-school-individu-${i}`).removeClass("d-none");
  $(`.content-delegation-school-individu-${i}`).empty();
  $(`.content-delegation-school-individu-${i}`).append(`
    <label for="school_delegation_individu${i}">
        School/Universities*
    </label>
    <input type="text" class="form-control" id="school_delegation_individu${i}" name="school_delegation_individu[]" placeholder="type school/universities">
  `);
};

const handlerSetContentDelegationOrganizationIndividu = (i) => {
  $(`.content-delegation-organization-individu-${i}`).removeClass("d-none");
  $(`.content-delegation-organization-individu-${i}`).empty();
  $(`.content-delegation-organization-individu-${i}`).append(`
    <label for="organization_delegation_individu${i}">
        Organization/Association*
    </label>
    <input type="text" class="form-control" id="organization_delegation_individu${i}" name="organization_delegation_individu[]" placeholder="type organization or association">
  `);
};

const handlerAddNewClubDelegationIndividu = (i) => {
  $(`.content-delegation-club-individu-${i}`).empty();
  $(`.content-delegation-club-individu-${i}`).append(`
    <label for="club_delegation_individu${i}">
        Club*
    </label>
    <p>
      <a href="javascript:void()" class="text-primary" onclick="handlerSetContentDelegationClubIndividu(${i})">
        Use what already have
      </a>
    </p>
    <input type="text" class="form-control" id="club_delegation_individu${i}" name="club_delegation_individu[]" placeholder="type club">
  `);
};

const handlerSetContentDelegationClubIndividu = async (
  i,
  default_value = ""
) => {
  const getDataClubs = await getClubs();
  let clubOptions = "";
  getDataClubs?.data?.map((val) => {
    clubOptions += `
       <option
          value="${val.id}">
          ${val.name}
      </option>
    `;
  });
  $(`.content-delegation-club-individu-${i}`).removeClass("d-none");
  $(`.content-delegation-club-individu-${i}`).empty();
  $(`.content-delegation-club-individu-${i}`).append(`
    <label for="club_delegation_individu${i}">
        Club*
    </label>
    <p>
      <a href="javascript:void()" class="text-primary" onclick="handlerAddNewClubDelegationIndividu(${i})">Add New Club+</a>
    </p>
    <select class="form-select js-select2-club-individu-delegation"
        id="club_delegation_individu${i}"
        name="club_delegation_individu[]"
        value="${!default_value ? "" : default_value}"
        required>
        <option value="" selected disabled>Choose Club</option>
        ${clubOptions}
    </select>
  `);

  $(".js-select2-club-individu-delegation").select2({
    selectionCssClass: "form-select",
  });
};

const getClubs = async () => {
  return await $.ajax({
    url: `${base_url}/api/get-clubs`,
    method: "GET",
    contentType: false,
    success: function (response) {
      try {
        return response.data;
      } catch (e) {
        alert("error get country!");
        return [];
      }
    },
    error: function (error) {
      alert(error.responseJSON.errors[x][0]);
      return [];
    },
  });
};

const getCountry = async () => {
  return await $.ajax({
    url: `${base_url}/api/get-country`,
    method: "GET",
    contentType: false,
    success: function (response) {
      try {
        return response.data;
      } catch (e) {
        alert("error get country!");
        return [];
      }
    },
    error: function (error) {
      alert(error.responseJSON.errors[x][0]);
      return [];
    },
  });
};

const getProvince = async (countryId) => {
  return await $.ajax({
    url: `${base_url}/api/get-state/${countryId}`,
    method: "GET",
    contentType: false,
    success: function (response) {
      try {
        return response.data;
      } catch (e) {
        alert("error get province!");
        return [];
      }
    },
    error: function (error) {
      alert(error.responseJSON.errors[x][0]);
      return [];
    },
  });
};

const getCity = async (countryId, provinceID) => {
  return await $.ajax({
    url: `${base_url}/api/get-city/${countryId}${
      !provinceID ? "" : "/" + provinceID
    }`,
    method: "GET",
    contentType: false,
    success: function (response) {
      try {
        return response.data;
      } catch (e) {
        alert("error get city!");
        return [];
      }
    },
    error: function (error) {
      alert(error.responseJSON.errors[x][0]);
      return [];
    },
  });
};
