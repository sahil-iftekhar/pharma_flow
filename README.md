# pharma_flow

pharmacist page: Using the current theme style, I want u to create a page under pharmacist where u can see pharmacists using javascript and plain vanilla css (don't use typescript or tailwind or shadcn), this page will call the getPharmacistsAction. if any error occurs set the error message in the state. In this page all the pharmacists will be in cards (u will make this pharmacist card under components/cards and it's styles also) and clicking on it will take me to pharmacist/[user_id] page (will talk about it below)

{
  "current_page": 1,
  "data": [
    {
      "id": 1,
      "user_id": 16,
      "license_num": 382692,
      "speciality": "Clinical Pharmacy",
      "bio": "Est cupiditate voluptas id voluptates voluptatum perspiciatis repellendus. Omnis et soluta expedita sed est dolore occaecati. Maiores quaerat doloribus ea repellat dolor et. Necessitatibus ut libero exercitationem asperiores veritatis alias.",
      "is_consultation": false,
      "user": {
        "id": 16,
        "first_name": "Ernest",
        "last_name": "Hintz",
        "email": "ffadel@example.org",
        "username": "dach.reese",
        "address": "767 Littel Corners Apt. 910\nWest Alysa, RI 51501",
        "is_active": true,
        "is_admin": false,
        "is_super_admin": false,
        "slug": "ffadel-at-exampleorg",
        "created_at": "2025-08-19T20:11:30.000000Z",
        "updated_at": "2025-08-19T20:11:30.000000Z"
      }
    },
    {
      "id": 2,
      "user_id": 17,
      "license_num": 792077,
      "speciality": "Community Pharmacy",
      "bio": "Nulla veniam sit non ut quae omnis. Ipsum minus nobis qui quasi neque minima dolorum. Cumque id quam quae dicta quos harum alias.",
      "is_consultation": false,
      "user": {
        "id": 17,
        "first_name": "Giovani",
        "last_name": "Ward",
        "email": "abogisich@example.org",
        "username": "maude.kohler",
        "address": "20375 Colleen Gateway\nMosheside, MO 46201-0938",
        "is_active": true,
        "is_admin": false,
        "is_super_admin": false,
        "slug": "abogisich-at-exampleorg",
        "created_at": "2025-08-19T20:11:30.000000Z",
        "updated_at": "2025-08-19T20:11:30.000000Z"
      }
    },
    {
      "id": 3,
      "user_id": 18,
      "license_num": 832776,
      "speciality": "Hospital Pharmacy",
      "bio": "Labore dolor pariatur distinctio maiores. Sequi illum enim hic expedita consequuntur sit. Eum praesentium molestiae sunt debitis aspernatur commodi.",
      "is_consultation": false,
      "user": {
        "id": 18,
        "first_name": "Micheal",
        "last_name": "Green",
        "email": "mrunolfsson@example.org",
        "username": "xbalistreri",
        "address": "8844 Mills Village\nFunkfurt, GA 26575-0588",
        "is_active": true,
        "is_admin": false,
        "is_super_admin": false,
        "slug": "mrunolfsson-at-exampleorg",
        "created_at": "2025-08-19T20:11:30.000000Z",
        "updated_at": "2025-08-19T20:11:30.000000Z"
      }
    },
    {
      "id": 4,
      "user_id": 19,
      "license_num": 996993,
      "speciality": "Hospital Pharmacy",
      "bio": "Minus dolor perspiciatis magni doloribus. Quos dolore ipsum est officia nemo.",
      "is_consultation": true,
      "user": {
        "id": 19,
        "first_name": "Chandler",
        "last_name": "Kunde",
        "email": "brenda.rath@example.org",
        "username": "gaylord.marjorie",
        "address": "5123 Streich Burgs\nRodriguezville, IN 82165",
        "is_active": true,
        "is_admin": false,
        "is_super_admin": false,
        "slug": "brendarath-at-exampleorg",
        "created_at": "2025-08-19T20:11:31.000000Z",
        "updated_at": "2025-08-19T20:11:31.000000Z"
      }
    },
    {
      "id": 5,
      "user_id": 20,
      "license_num": 934858,
      "speciality": "Clinical Pharmacy",
      "bio": "Quis dolore natus beatae vel. Praesentium et aperiam dolore sapiente adipisci. Doloribus qui distinctio quia id. Cupiditate reprehenderit dicta dolorum autem eveniet ut delectus. Qui et at doloremque aut molestiae.",
      "is_consultation": true,
      "user": {
        "id": 20,
        "first_name": "Marge",
        "last_name": "Swift",
        "email": "nestor.ratke@example.net",
        "username": "esatterfield",
        "address": "60795 Nelda Flats Suite 097\nO'Konfort, DE 14459-7547",
        "is_active": true,
        "is_admin": false,
        "is_super_admin": false,
        "slug": "nestorratke-at-examplenet",
        "created_at": "2025-08-19T20:11:31.000000Z",
        "updated_at": "2025-08-19T20:11:31.000000Z"
      }
    }
  ],
  "first_page_url": "http://localhost:8000/api/pharmacists?page=1",
  "from": 1,
  "last_page": 1,
  "last_page_url": "http://localhost:8000/api/pharmacists?page=1",
  "links": [
    {
      "url": null,
      "label": "&laquo; Previous",
      "active": false
    },
    {
      "url": "http://localhost:8000/api/pharmacists?page=1",
      "label": "1",
      "active": true
    },
    {
      "url": null,
      "label": "Next &raquo;",
      "active": false
    }
  ],
  "next_page_url": null,
  "path": "http://localhost:8000/api/pharmacists",
  "per_page": 10,
  "prev_page_url": null,
  "to": 5,
  "total": 5
}

this is the response of getPharmacistsAction

u will use title status problem id in the card. u will have a button (only super_admin can see this (u can get the role using getUserRoleAction)) to add a pharmacist named CreatePharmacist (which will call the server action and set state for errors and success message) (the button will be in components/buttons and create the styles also) (will talk about it's functionality below)
everything will be made using javascript and plain vanilla css (don't use typescript or tailwind or shadcn)

{
    page
integer
(query)
Page number for pagination

}

my query parameters 
next in the bottom of the page there will be will a pagination (use the pagination u already created in components/pagination and create the styles also) which will request a get request when click to another page number

Create Pharmacist button: using javascript and plain vanilla css (don't use typescript or tailwind or shadcn) this button will open a modal (in components/modals and create the styles also) where u will be able to create a pharmacist using a form (in components/forms and create the styles also). use useFormStatus for the button CreatePharmacist inside the form (in components/buttons/buttons and create the styles also) and the form should have all the fields in the createPharmacistAction and should have a method in form action field handlesubmit (dont use useactionstate, use usestate, userouter etc) which will call the server action createPharmacistAction and set state for errors and success message

pharmacist/[user_id] page: using javascript and plain vanilla css (don't use typescript or tailwind or shadcn) this page will take the pharmacist id from the orginal page as u can see in getPharmacistsAction I get the id and then use getPharmacistAction (will take user_id as parameter not id) for retrieving all the details

{
  "id": 5,
  "user_id": 20,
  "license_num": 934858,
  "speciality": "Clinical Pharmacy",
  "bio": "Quis dolore natus beatae vel. Praesentium et aperiam dolore sapiente adipisci. Doloribus qui distinctio quia id. Cupiditate reprehenderit dicta dolorum autem eveniet ut delectus. Qui et at doloremque aut molestiae.",
  "is_consultation": true,
  "user": {
    "id": 20,
    "first_name": "Marge",
    "last_name": "Swift",
    "email": "nestor.ratke@example.net",
    "username": "esatterfield",
    "address": "60795 Nelda Flats Suite 097\nO'Konfort, DE 14459-7547",
    "is_active": true,
    "is_admin": false,
    "is_super_admin": false,
    "slug": "nestorratke-at-examplenet",
    "created_at": "2025-08-19T20:11:31.000000Z",
    "updated_at": "2025-08-19T20:11:31.000000Z"
  }
}

this is the response of getPharmacistAction

now make a card where u will show all the details for the pharmacist (in components/cards and create the styles also). then there will be two buttons update and delete button (in components/buttons/buttons and create the styles also), the UpdatePharmacistButton will open a modal (in components/modals and create the styles also) where u will be able to update a pharmacist using a form (u can use the form u already built for creating but make sure all the fields are already populated using the getPharmacistAction response) use useFormStatus for the button UpdatePharmacist inside the form (in components/buttons/buttons and create the styles also) and the form should have all the fields in the updatePharmacistAction and should have a method in form action field handlesubmit (dont use useactionstate, use usestate, userouter etc) which will call the server action updatePharmacistAction (will take user_id as parameter not id) and set state for errors and success message, the delete button will open a modal deleteModal which will have a confirmation message and yes and no. If yes is preseed it will call the server action deleteUserAction (will take user_id as parameter not id) and then set state for errors and success message and then redirect to pharmacist page

(each of the files should have its own css and the css file should be in module.css)

