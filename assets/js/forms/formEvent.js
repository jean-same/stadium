import moment from "moment";

const app = {
  errors: false,

  init: () => {
    console.log("init");
    let formEvent = document.querySelector(".event-form");

    formEvent.addEventListener("submit", app.handleSubmit);
  },

  handleSubmit: function (evt) {
    evt.preventDefault();

    const formElement = evt.currentTarget;
    let allInputValues = [];

    const eventName = formElement.querySelector("#event_name").value;
    const eventAddress = formElement.querySelector("#event_place").value;
    const eventStartDate = new Date(
      formElement.querySelector("#event_startDate").value
    );
    const eventEndDate = new Date(
      formElement.querySelector("#event_endDate").value
    );
    const eventSchedule = new Date(
      formElement.querySelector("#event_schedule").value
    );
    const eventMaxParticipants = formElement.querySelector(
      "#event_maxParticipants"
    ).value;

    allInputValues.push(
      eventName,
      eventAddress,
      eventStartDate,
      eventEndDate,
      eventSchedule,
      eventMaxParticipants
    );

    const today = new Date();

    const todayDateFormated = today.getTime();
    const startDateFormated = eventStartDate.getTime();
    const endDateFormated = eventEndDate.getTime();

    //let errors = false;
    let data = {};
    let errors = false;

    let allErrorsDiv = document.querySelectorAll(".form-errors");

    for (let errorDiv of allErrorsDiv) {
      errorDiv.innerHTML = "";
      errorDiv.style.display = "none";
    }

    for (let input of allInputValues) {
      if (input == "") {
        errors = true;
        let errorsDiv = document.querySelector(".div-default-error");
        errorsDiv.innerHTML = "";
        errorsDiv.style.display = "block";

        let p = document.createElement("p");
        p.textContent = "Tous les champs sont obligatoires";
        errorsDiv.appendChild(p);
      }
    }

    if (!errors) {
      let dataErrors = app.dataErrors(
        data,
        eventName,
        eventAddress,
        startDateFormated,
        todayDateFormated,
        endDateFormated,
        eventMaxParticipants
      );

      if (Object.keys(dataErrors) && !errors) {
        console.log("test ok");
        Object.keys(dataErrors).forEach((key) => {
          errors = true;
          let errorsDiv = document.querySelector(".form-error-" + key);
          let p = document.createElement("p");
          errorsDiv.style.display = "block";
          p.textContent = data[key];
          errorsDiv.appendChild(p);
        });
      }
    }

    if (!errors) {
      console.log(dataErrors);
      document.querySelector(".event-form").submit();
    }
  },

  dataErrors: function (
    data,
    eventName,
    eventAddress,
    startDateFormated,
    todayDateFormated,
    endDateFormated,
    eventMaxParticipants
  ) {
    //let data = {};

    if (eventName.length <= 2) {
      data.name = "Le nom doit faire au moins 3 caracteres";
    }

    if (eventAddress == "") {
      data.place = "L'adresse ne doit pas etre vide";
    }

    if (startDateFormated < todayDateFormated) {
      data.startDate = "La date de debut doit etre superieur à la date du jour";
    } else if (startDateFormated > endDateFormated) {
      data.startDate =
        "La date de debut doit etre inferieur ou egale à la date de fin";
    }

    if (endDateFormated < todayDateFormated) {
      data.endDate = "La date de fin doit etre superieur à la date du jour";
    } else if (endDateFormated < startDateFormated) {
      data.endDate =
        "La date de fin doit etre superieur ou egale à la date de debut";
    }

    if (eventMaxParticipants <= 0 || eventMaxParticipants > 1000) {
      data.maxParticipants =
        "Le nombre max de participants doit etre entre 1 et 1000";
    }

    return data;
  },

  /*
  cleanAllInputValues: function (allInputValues) {
    for (let input of allInputValues) {
      if (input == "") {
        app.errors = true;
        let errorsDiv = document.querySelector(".div-default-error");
        errorsDiv.innerHTML = "";
        errorsDiv.style.display = "block";

        let p = document.createElement("p");
        p.textContent = "Tous les champs sont obligatoires";
        errorsDiv.appendChild(p);
      }
    }
  },
*/
  formatDate: (str) => {
    return moment(str).format("DD/MM/YYYY");
  },
};

document.addEventListener("DOMContentLoaded", app.init);
