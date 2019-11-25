/*
 * Handles the click event for the form.
 *
 * param e  A reference to the event object
 * return   True allows the reset to happen; False prevents
 *          the browser from resetting the form.
 */
function deleteItem(e)
{
	// Confirm that the user wants to reset the form.
	if ( confirm('Are you sure?') )
	{

		return true;
	}

	// Prevents the form from submit
	e.preventDefault();

	// When using onReset="resetForm()" in markup, returning false would prevent
	// the form from resetting
	return false;
}

/*
 * Handles the submit event of the survey form
 *
 * param e  A reference to the event object
 * return   True if no validation errors; False if the form has
 *          validation errors
 */
function validate(e)
{
	//	Hides all error elements on the page
	hideAllErrors();

	// Giang: 14/09/2019: Comment for test
	//	Determine if the form has errors
	if(formHasErrors()){
		// 	Prevents the form from submitting
		e.preventDefault();

		// 	Returning false prevents the form from submitting
		return false;
	}

	return true;
}

function validateLogin(e)
{
	//	Hides all error elements on the page
	hideAllErrors();

	// Giang: 14/09/2019: Comment for test
	//	Determine if the form has errors
	if(formLoginHasErrors()){
		// 	Prevents the form from submitting
		e.preventDefault();

		// 	Returning false prevents the form from submitting
		return false;
	}

	return true;
}

function validateUpdateUser(e)
{
	//	Hides all error elements on the page
	hideAllErrors();

	// Giang: 14/09/2019: Comment for test
	//	Determine if the form has errors
	if(formUpdateUserHasErrors()){
		// 	Prevents the form from submitting
		e.preventDefault();

		// 	Returning false prevents the form from submitting
		return false;
	}

	return true;
}

function validateChangePassword(e)
{
	//	Hides all error elements on the page
	hideAllErrors();

	// Giang: 14/09/2019: Comment for test
	//	Determine if the form has errors
	if(formChangePasswordHasErrors()){
		// 	Prevents the form from submitting
		e.preventDefault();

		// 	Returning false prevents the form from submitting
		return false;
	}

	return true;
}


/*
 * Hides all of the error elements.
 */
function hideAllErrors()
{
	let errorFields = document.getElementsByClassName("error");
	for(let i=0; i < errorFields.length; i++){
		errorFields[i].style.display = "none";
	}
}

/*
 * Does all the error checking for the form.
 *
 * return   True if an error was found; False if no errors were found
 */
function formHasErrors()
{
	// Code below here
	let errorFlag = false;

	let requiredFields = ["email","password","confirm"];


	for(let i=0; i < requiredFields.length; i++){
		let textField = document.getElementById(requiredFields[i]);

		if(!formFieldHasInput(textField)){
			document.getElementById(requiredFields[i] + "_error").style.display = "block";
			errorFlag = true;
		}else{

			if(requiredFields[i] == "email") errorFlag = emailAddressIsInvalid(errorFlag);
			if(requiredFields[i] == "confirm") errorFlag = matchConfirmAndPassword(errorFlag);
		}
	}

	return errorFlag;
}


/*
 * Does all the error checking for the form.
 *
 * return   True if an error was found; False if no errors were found
 */
function formLoginHasErrors()
{
	// Code below here
	let errorFlag = false;

	let requiredFields = ["email","password"];


	for(let i=0; i < requiredFields.length; i++){
		let textField = document.getElementById(requiredFields[i]);

		if(!formFieldHasInput(textField)){
			document.getElementById(requiredFields[i] + "_error").style.display = "block";
			errorFlag = true;
		}else{

			if(requiredFields[i] == "email") errorFlag = emailAddressIsInvalid(errorFlag);
		}
	}

	return errorFlag;
}

/*
 * Does all the error checking for the form.
 *
 * return   True if an error was found; False if no errors were found
 */
function formUpdateUserHasErrors()
{
	// Code below here
	let errorFlag = false;

	let requiredFields = ["email"];

	for(let i=0; i < requiredFields.length; i++){
		let textField = document.getElementById(requiredFields[i]);

		if(!formFieldHasInput(textField)){
			document.getElementById(requiredFields[i] + "_error").style.display = "block";
			errorFlag = true;
		}else{

			if(requiredFields[i] == "email") errorFlag = emailAddressIsInvalid(errorFlag);
		}
	}

	return errorFlag;
}

function formChangePasswordHasErrors()
{
	// Code below here
	let errorFlag = false;

	let requiredFields = ["password"];

	for(let i=0; i < requiredFields.length; i++){
		let textField = document.getElementById(requiredFields[i]);

		if(!formFieldHasInput(textField)){
			document.getElementById(requiredFields[i] + "_error").style.display = "block";
			errorFlag = true;
		}
	}

	return errorFlag;
}

/*
 * Determines if a text field element has input
 *
 * param   fieldElement A text field input element object
 * return  True if the field contains input; False if nothing entered
 */
function formFieldHasInput(fieldElement)
{
	// Check if the text field has a value
	if ( fieldElement.value == null || trim(fieldElement.value) == "" )
	{
		// Invalid entry
		return false;
	}

	// Valid entry
	return true;
}

/**
*   Does validate an email address
* 	return   True and displays Wanrning if the email address is invalid;
*            if the email address is valid, not change the value of errorFlag, just return the value of errorFlag was passed.
*/
function emailAddressIsInvalid(errorFlag)
{

	// Validate Email Addess
	// http://emailregex.com
	let regexEmail = new RegExp(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/i);

	let emailAddress = document.getElementById("email").value;

	if(!regexEmail.test(emailAddress)){
		document.getElementById("emailformat_error").style.display = "block";

		if(!errorFlag){
			document.getElementById("email").focus();
			document.getElementById("email").select();
		}

		errorFlag = true;
	}

	return errorFlag;
}

/**
 *  Validate Password and Confirm
 */
function matchConfirmAndPassword(errorFlag)
{
	let password = document.getElementById("password").value;
	let confirm = document.getElementById("confirm").value;

	if(password != confirm)
	{
		document.getElementById("confirmmatch_error").style.display = "block";
		errorFlag = true;
	}

	return errorFlag;
}

/*
 * Removes white space from a string value.
 *
 * return  A string with leading and trailing white-space removed.
 */
function trim(str)
{
	// Uses a regex to remove spaces from a string.
	return str.replace(/^\s+|\s+$/g,"");
}

/*
 * Handles the load event of the document.
 */
function load()
{
	let submitdelete = document.getElementById("sumbitdelete");
	let deleteuser = document.getElementById("deleteuser");
	let registernormaluser = document.getElementById("registernormaluser");
	let adduser = document.getElementById("adduser");
	let login = document.getElementById("login");
	let updateuser = document.getElementById("updateuser");
	let changepassword = document.getElementById("changepassword");

	var x = document.getElementsByClassName("moderate_del");
	if(x != null)
		for (var i = 0; i < x.length; i++) {
		  x[i].addEventListener("click",deleteItem);
		}

	// Add event listener for the form submit
	if(submitdelete != null)
		submitdelete.addEventListener("click",deleteItem);

	// Add event listener for the form submit
	if(deleteuser != null)
		deleteuser.addEventListener("click",deleteItem);

	// Add event listener for the form submit
	if(registernormaluser != null)
		registernormaluser.addEventListener("click", validate, false);

	// Add event listener for the form submit
	if(adduser != null)
		adduser.addEventListener("click", validate, false);

	// Add event listener for the form submit
	if(login != null)
		login.addEventListener("click", validateLogin, false);

	// Add event listener for the form submit
	if(updateuser != null)
		updateuser.addEventListener("click", validateUpdateUser, false);

	// Add event listener for the form submit
	// if(changepassword != null)
	// 	changepassword.addEventListener("click", validateChangePassword);


}

// Add document load event listener
document.addEventListener("DOMContentLoaded", load);
