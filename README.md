## Example
```html
<form action="<?php echo $this->getFormActionUrl() ?>" class="form" id="newsletter-validate-detail">
    <div class="input-box">
        <img src="<?php echo $this->getSkinUrl('images/loading.svg') ?>" alt="Loading" id="newsletter_loading" class="loading">
        <input type="email" placeholder="Informe seu e-mail" id="newsletter_input" class="input required-entry validate-email" autocapitalize="off" autocorrect="off" spellcheck="false">
        <div id="newsletter_message" class="message"></div>
    </div>
    <button type="submit" class="submit" id="newsletter_submit">Assinar</button>
</form>
```
```javascript
//<![CDATA[
	var newsletterSubscriberFormDetail = new VarienForm('newsletter-validate-detail'); 
//]]>

jQuery(document).ready(function($){
    $('#newsletter_submit').click(function(e){
        e.preventDefault();
                
        var input = $("#newsletter_input");
        var url = $('#newsletter-validate-detail').attr('action');
        var message = $("#newsletter_message");
        var button = $('#newsletter_submit');
        var loading = $("#newsletter_loading");
        url = url.replace('newsletter/subscriber/new', 'newsletterajax/subscriber/new');

        // Zera parametros
        message.removeClass('success').removeClass('warning').removeClass('error').removeClass('active');
        button.removeClass('disabled').prop("disabled", false);
        loading.removeClass('active');
                
        if(newsletterSubscriberFormDetail.validator.validate()){
            try{
            	button.addClass('disabled').prop("disabled",true);
            	loading.addClass('active');
                
                $.ajax({
                    url: url,
                    dataType: 'json',
                    type: 'POST',
                    data: {email: input.val()},
                    success: function (data){
                    	message.addClass('active');
                    	button.removeClass('disabled').prop("disabled", false);
                    	loading.removeClass('active');

                        if(data.status == "SUCCESS"){
                        	message.addClass('success').html(data.message);
                        	input.val("").blur();
                        }else if(data.status == "WARNING"){
                        	message.addClass('warning').html(data.message);
                        }else{
                        	message.addClass('error').html(data.message);
                        }
                    },
                    complete: function(){
                        setTimeout(function(){
                            message.removeClass('success').removeClass('warning')
                            message.removeClass('error').removeClass('active'); 
                        }, 10000);
                    }
                });
            }catch (e){
            	message.addClass('active').addClass('error')
            	message.html("Erro inesperado, consulte console log para mais detalhes");
            	console.log(e);
            }
        }
    })
});
```