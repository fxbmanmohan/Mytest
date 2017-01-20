// Require jQuery
// Use jQuery.cookie if you want to restore the previous expansion of the tree

jQuery.fn.tree = function(options) {

  // Setup default options
  /* Avaiable options are:
   *  - open_char: defeault UTF8 character on open node.
   *  - close_char: defeault UTF8 character on close node.
   *  - default_expanded_paths_string: if no cookie found the tree will be expanded with this paths string (default '')
   *      To expand all children use 'all'.
   *      To close all the tree use ''.
   *      To expand the first child and the second child of the first child use '0/1'
   *  - only_one: if this option is true only one child will be expanded at time (default false)
   *  - animation: animation used to expand a child (default 'slow')
   */

  if(options === undefined || options === null) options = {};
  var default_options = {
    open_char : '<img src="images/minus-icon.png">',
    close_char : '<img src="images/plus-icon.png">',
    default_expanded_paths_string : '',
    only_one : false,
    animation : 'slow'
  };
  var o = {};
  jQuery.extend(o, default_options, options);

  // Get the expanded paths from the current state of tree
  jQuery.fn.save_paths = function() {
    var paths = [];
    var path = [];
    var open_nodes = jQuery(this).find('li span.open');
    var last_depth = null;
    for(var i = 0; i < open_nodes.length; i++) {
      var depth = jQuery(open_nodes[i]).parents('ul').length-1;
      if((last_depth == null && depth > 0) || (depth > last_depth && depth-last_depth > 1))
        continue;
      var pos = jQuery(open_nodes[i]).parent().prevAll().length;
      if(last_depth == null) {
        path = [pos];
      } else if(depth < last_depth) {
        paths.push(path.join('/'));
        var diff = last_depth - depth;
        path.splice(path.length-diff-1, diff+1);
        path.push(pos);
      } else if(depth == last_depth) {
        paths.push(path.join('/'));
        path.splice(path.length-1, 1);
        path.push(pos);
      } else if(depth > last_depth) {
        path.push(pos);
      }
      last_depth = depth;
    }
    paths.push(path.join('/'));
    // Save state to cookie
    var cookie_key = this.data('cookie');
    if(cookie_key === null) cookie_key = 'jtree-cookie';
    try { jQuery.cookie(cookie_key, paths.join(',')); }
    catch(e) {}
  };

  // This function expand the tree with 'path'
  jQuery.fn.restore_paths = function() {
    var paths_string = null;
    var cookie_key = this.data('cookie');
    if(cookie_key === null) cookie_key = 'jtree-cookie';
    try { paths_string = jQuery.cookie(cookie_key); }
    catch(e) {}
    if(paths_string === null || paths_string === undefined) paths_string = o.default_expanded_paths_string;
    if(paths_string == 'all') {
      jQuery(this).find('span.jtree-arrow').open();
    } else {
      var paths = paths_string.split(',');
      for(var i = 0; i < paths.length; i++) {
        var path = paths[i].split('/');
        var obj = jQuery(this);
        for(var j = 0; j < path.length; j++) {
          obj = jQuery(obj.children('li')[path[j]]);
          obj.children('span.jtree-arrow').open();
          obj = obj.children('ul')
        }
      }
    }
  };

  // Open a child
  jQuery.fn.open = function(animate) {
    if(jQuery(this).hasClass('jtree-arrow')) {
      jQuery(this).parent().children('ul').show(animate);
      jQuery(this).removeClass('close');
      jQuery(this).addClass('open');
      jQuery(this).html(o.open_char);
    }
  };

  // Close a child
  jQuery.fn.close = function(animate) {
    if(jQuery(this).hasClass('jtree-arrow')) {
      jQuery(this).parent().children('ul').hide(animate);
      jQuery(this).removeClass('open');
      jQuery(this).addClass('close');
      jQuery(this).html(o.close_char);
    }
  };

  // Click event on <span class="jtree-arrow"></span>
  jQuery.fn.click_event = function() {
    var button = jQuery(this);
    if(button.hasClass('jtree-arrow')) {
      if(button.hasClass('open')) {
        button.close(o.animation);
        if(o.only_one) button.closest('li').siblings().children('span.jtree-arrow').close(o.animation);
      } else if(button.hasClass('close')) {
        button.open(o.animation);
        if(o.only_one) button.closest('li').siblings().children('span.jtree-arrow').close(o.animation);
      }
    }
  };

  for(var i = 0; i < this.length; i++) {
    if(this[i].tagName === 'UL') {
      // Make a tree
      jQuery(this[i]).find('li').has('ul').prepend('<span class="jtree-arrow close" style="cursor:pointer;">' + o.close_char + '</span>');
      jQuery(this[i]).find('ul').hide();
      // Restore cookie expand path
//      jQuery(this[i]).restore_paths();
      // Click event for arrow
      jQuery(this[i]).find('li > span.jtree-arrow').on('click', {tree : this[i]}, function(e) {
        jQuery(this).click_event();
        jQuery(e.data.tree).save_paths();
      });
      // Click event for 'jtree-button'
      jQuery(this[i]).find('li .jtree-button').on('click', {tree : this[i]}, function(e) {
        var arrow = jQuery(this).closest('li').children('span.jtree-arrow');
        arrow.click_event();
        jQuery(e.data.tree).save_paths();
      });
    }
  }
}
