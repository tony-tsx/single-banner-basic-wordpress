<div id="single-banner-container">
  <h2>
    Banners do site
  </h2>
  <?php if ( count( get_single_banner_slugs() ) ): ?>
    <div class="row">
      <?php foreach( get_single_banner_slugs() as $slug ): ?>
        <div class="col-6">
          <div class="card">
            <div class="card-title">
              <?= get_single_banner_area_label( $slug ) ?>
            </div>
            <?php if ( has_single_banner( $slug ) ): ?>
              <a href="#" class="upload-attachment card-img-top" id="image-container-<?=$slug?>" data-area="<?=$slug?>">
                <img src="<?=get_single_banner_src( $slug )?>" class="card-img-top">
              </a>
            <?php else: ?>
              <a href="#" class="upload-attachment" data-area="<?=$slug?>" id="image-container-<?=$slug?>"></a>
            <?php endif; ?>
            <div class="card-body">
              <div class="card-text">
                <?= get_single_banner_area_description( $slug ) ?>
              </div>
              <a href="#" class="card-link upload-attachment" data-area="<?=$slug?>">Escolher banner</a>
              <a href="#" class="card-link remove-attachment <?= has_single_banner( $slug ) ? '': 'd-none' ?>" data-area="<?=$slug?>">Remover banner</a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <h5>Nenhuma área disponivel</h5>
  <?php endif; ?>
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
          var area = button.data( 'area' )

          $( '#image-container-' + area )
            .html( '<img src="' + attachment.url + '" class="card-img-top">' )

          $( '.remove-attachment' ).removeClass( 'd-none' )

          $.ajax( {
            url: single_banner_utils.wp_action_url,
            type: 'POST',
            data: {
              nonce: single_banner_utils.nonce,
              action: 'update_single_banner',
              'image-id': attachment.id,
              area: area
            }
          } )

        } )
        .open()
    } )

    $( 'body' ).on( 'click', '.remove-attachment', function( e ) {

      e.preventDefault()

      var button = $( this ).addClass( 'd-none' )
      var area = button.data( 'area' )

      $( '#image-container-' + area ).html( '' )

      $.ajax( {
        url: single_banner_utils.wp_action_url,
        type: 'POST',
        data: {
          nonce: single_banner_utils.nonce,
          action: 'remove_single_banner',
          area: area
        }
      } )
    } )

  } )
</script>