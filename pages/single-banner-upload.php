<div id="single-banner-container">
  <h2>
    Banner do site
  </h2>
  <div class="row">
    <div class="col-12">
      <a href="#" class="upload-attachment">
        <?= has_single_banner() ? get_single_banner() : 'Escolher banner' ?>
      </a>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <a href="#" class="remove-attachment <?= has_single_banner() ? '' : 'd-none' ?>">Remove image</a>
    </div>
  </div>
</div>

<script>
  jQuery(function($) {

    $('body').on( 'click', '.upload-attachment', function( e ) {

      e.preventDefault()

      var button = $(this)
      var custom_uploader = wp.media( {
          title: 'Escolher banner',
          library: { type: 'image' },
          button: { text: 'Utilizar imagem' },
          multiple: false
        } )
        .on( 'select', function() {
          var attachment = custom_uploader.state().get( 'selection' ).first().toJSON()
          button.html( '<img src="' + attachment.url + '">' )
            .next().val( attachment.id ).next().show()
          $( '.remove-attachment' ).removeClass( 'd-none' )
          $.ajax( {
            url: single_banner_utils.wp_action_url,
            type: 'POST',
            data: {
              nonce: single_banner_utils.nonce,
              action: 'update_single_banner',
              'image-id': attachment.id
            }
          } )

        } )
        .open()
    } )

    $( 'body' ).on( 'click', '.remove-attachment', function( e ) {

      $( '.upload-attachment' ).html( 'Escolher banner' )

      e.preventDefault()

      var button = $( this )
      button.addClass( 'd-none' )

      $.ajax( {
        url: single_banner_utils.wp_action_url,
        yype: 'POST',
        data: {
          nonce: single_banner_utils.nonce,
          action: 'remove_single_banner'
        }
      } )
    } )

  } )
</script>