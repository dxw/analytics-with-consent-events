/* globals cookieControlAnalyticsEvents */
/* eslint-disable */
var analyticsWithConsentEvents = {

  init: function() {    
    window.analyticsWithConsent.gaAccept(); // need to re-instate main analytics-with-consent script
    window.analyticsWithConsentEvents.gaAddEvents();
  },

  gaAddEvents: function() {    
    window.analyticsWithConsentEvents.gaAddOutboundEvents();
    window.analyticsWithConsentEvents.gaAddDownloadEvents();
  },
  gaAddOutboundEvents: function() {    
    let siteurl = cookieControlAnalyticsEvents.siteurl;

    // check links
    jQuery('a[href]').each(function() {
      // check if outbound (to another domain)
      let isOutbound = false;
      let fulllink = this.getAttribute('href');
      let link = fulllink;
      if (link.indexOf('?') !== -1) {
        link = link.substr(0, link.indexOf('?'));
      }
      let target = '';
      if (this.hasAttribute('target')) {
        target = this.getAttribute('target').toLowerCase();
      }

      if (link.indexOf('http') !== -1) {
        if (link.indexOf(siteurl) !== 0) {
          isOutbound = true;
        }
      }
      // if outbound, add ga event
      if (isOutbound) {
        this.onclick = function() {
          let label = fulllink;
          ga('send', 'event', 'outbound-link', fulllink, label, 0,
            {
              'nonInteration':false,
              'transport': 'beacon',
            });
        }
      }
    });
  },
  gaAddDownloadEvents: function() {

    var docs = [
      'pdf','docx','doc','xlsx','xls','pptx','ppt','dot','dotx',
      'odt','fodt','ods','fods','odp','fodp','odg','fodg','odf','ott',
      'txt','epub','rtf','csv','xml',
      'zip','rar',
      'mp4','mp3','webm','wav','mpg','mpeg','wma','ogg','mid','midi','m3u','3gp','flv','mov',
      'png','jpg','gif','jpeg','svg','bmp','tif','tiff','eps'
    ];

    // try determine if a document link in various ways:
      // if a wp-content/uploads link
      // not an #anchor link or url with trailing slash
      // if extension matches a document type
    function isDocumentLink(link) {
      isDocument = false;
      if (link.indexOf('wp-content/uploads') !== -1) {
        isDocument = true;
      }
      let trailingSlash = link.substr(link.length-1) == '/';
      let anchorLink = link.substr(0,1) == '#';
      let extension = getExtension(link);
      if (!trailingSlash && !anchorLink) {
        for (let c = 0; c < docs.length; c++) {
          if (extension == docs[c]) {
            isDocument = true;
            break;
          }
        }
      }
      return isDocument;
    }

    // try determine extension (assuming last 3 or 4 characters after '.')
    function getExtension(link) {
      let extension = '';
      if (link.indexOf('.') !== -1) {
        let parts = link.split('.');
        if (parts.length > 0) {
          let finalpart = parts[parts.length-1];
          if (finalpart.length == 3 || finalpart.length == 4) {
            extension = finalpart;
          }
        }
      }
      return extension.toLowerCase();
    }

    // check links
    jQuery('a[href]').each(function() {
      let fulllink = this.getAttribute('href');
      let link = fulllink;
      if (link.indexOf('?') !== -1) {
        link = link.substr(0, link.indexOf('?'));
      }
      let isDocument = isDocumentLink(link);
      if (!isDocument) { return; }

      // if a document link, add ga event
      if (isDocument) {
        let target = '';
        if (this.hasAttribute('target')) {
          target = this.getAttribute('target').toLowerCase();
        }
        let extension = getExtension(link);

        this.onclick = function() {
          let label = fulllink;
          if (extension != '') {
            label += ' (' + extension + ')';
          }
          ga('send', 'event','download', fulllink, label , 0,
            {
              'nonInteration':false,
              'transport': 'beacon',
            });
        }
      }
    });
  },  
};
/* eslint-enable */