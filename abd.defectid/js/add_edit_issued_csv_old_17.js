$(document).ready(function() {
	
	var validator = $("#addissueto").validate({
	rules:
	{  
	   company_name:
	   {
	   		required: true
	   },
	   contact_name:
	   {
	   		required: true
	   },
	   phone:
	   {
	   		required: true,
			number:true,
			minlength:10,
			maxlength:13
	   },
	   
	   emailid:
	   {
	   		required: true,
			email:true
	   }
	},
	messages:
	{
		company_name:
		{
			required: '<div class="error-edit-profile">The company name field is required</div>'
			//email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		contact_name:
		{
			required: '<div class="error-edit-profile">The contact name field is required.</div>'
			
		},
		phone:
		{
			required: '<div class="error-edit-profile">The phone field is required</div>',
			number:'<div class="error-edit-profile">The phone field should be number</div>',
			minlength:'<div class="error-edit-profile">The phone field minimum length is 10 digit</div>',
			maxlength:'<div class="error-edit-profile">The phone field maximum length is 13 digit</div>'
			
			
		},
		
		emailid:
		{
			required: '<div class="error-edit-profile">The email field is required</div>',
			email: '<div class="error-edit-profile">The email is not valid format.</div>'
			
		},
		
		
		debug:true
	}
	
	});
	jQuery.validator.addMethod("alpha", function( value, element ) {
		return this.optional(element) || /^[a-zA-Z ]+$/.test(value);
	}, "Please use only alphabets (a-z or A-Z)");
	jQuery.validator.addMethod("numeric", function( value, element ) {
		return this.optional(element) || /^[0-9]+$/.test(value);
	}, "Please use only numeric values (0-9)");
	jQuery.validator.addMethod("alphanumeric", function( value, element ) {
		return this.optional(element) || /^[a-z A-Z0-9]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 characters");
	jQuery.validator.addMethod("mobile", function( value, element ) {
		return this.optional(element) || /^[ 0-9+-]+$/.test(value);
	}, "You can use only 0-9 - + characters");
	jQuery.validator.addMethod("login", function( value, element ) {
		return this.optional(element) || /^[A-Za-z0-9_.]+$/.test(value);
	}, "You can use only a-z A-Z 0-9 _ and . characters");
	
});
// JavaScript Document// JavaScript Document