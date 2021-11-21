
/* console.log('on est la');

let form = document.querySelector('.activity-form'); */
//console.log(form)


const app = {

    init: () => {
        console.log('init');
        let form = document.querySelector('.activity-form');
        form.addEventListener('submit', app.handleSubmit)

      },

      handleSubmit: function(evt) {
        evt.preventDefault();

        const formElement = evt.currentTarget;

        const activityNameField = formElement.querySelector('#activity_name');
        const activityName = activityNameField.value.toString();


        const activityPictureField = formElement.querySelector('#activity_picture');
        const activityPicture = activityPictureField.value ;
        let errors = [];

        if(activityName.length < 6 ){
            errors.push("Au moins 7 caracters")
        }

        if(activityPicture == ""){
            errors.push("Peut pas etre vide")
        }

        if(errors.length > 0){
            let errorsDiv = document.querySelector('.form-errors');

            errorsDiv.innerHTML = "";
            for(let error of errors){
                
                errorsDiv.style.display = "block";

                let p = document.createElement('p');
                p.textContent = "- " +  error
                errorsDiv.appendChild(p);
            }
        } else {
            document.querySelector('.activity-form').submit();
            activityPicture.value = "";
            activityName.value = "";
        }

      }
}


document.addEventListener('DOMContentLoaded', app.init); 
