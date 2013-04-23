Easy Freeform Dependent Recipient
=================================

An ExpressionEngine extensions that allows you to set a Freeform field
that governs who receives emails from the form.

API
---

To use the extension, simply supply the Freeform field name you want 
to track as the value for `easy_dependent_recipient_field` and then
supply a numbered series of `easy_dependent_recipient_value`s and
corresponding `easy_dependent_recipient_email`s. The extension will
send to the email address that corresponds to the value the user
chooses when filling out the form. Values and emails are suffixed by
a number that relates them. **The numbering must begin with 1.**

	{exp:freeform:form
		form_name="lead_generation_form"
		recipients="yes"
		easy_dependent_recipient_field="favorite_fruit"
		easy_dependent_recipient_value1="Apples"
		easy_dependent_recipient_email1="recipient-for-apples@domain.com"
		easy_dependent_recipient_value2="Oranges"
		easy_dependent_recipient_email2="recipient-for-oranges@domain.com"
		}

In this example, if a user chooses "Apples", recipient-for-apples@domain.com
gets sent the email, but the field is also tracked in Freeform, so it’s value 
remains available for posterity.