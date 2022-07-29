function add_domain_to_canvas( ctx, x,y,w,h, color, label, title, description ){

    if( !ctx.hasOwnProperty('domains') ){
        ctx.domains = []
    }
    ctx.domains.push( [x,y,w,h,title,description] )

    var grd = ctx.createLinearGradient(x, y, x, y+h);
    grd.addColorStop(0, "white");
    grd.addColorStop(.5, color);
    grd.addColorStop(1, "white");
    ctx.fillStyle = grd;
    ctx.strokeStyle = color;   

    build_path_for_domain_on_canvas(ctx,x,y,w,h)
    ctx.fill();
    ctx.stroke();  

    ctx.fillStyle = 'black';
    ctx.fillText(label, x+2, h-4);
}

function build_path_for_domain_on_canvas(ctx,x,y,w,h){
    ctx.beginPath();
    ctx.moveTo( x,y );
    ctx.lineTo( x+w,y );
    ctx.arc( x+w,y+h/2, h/2, 1.5*Math.PI, 0.5*Math.PI );
    ctx.lineTo( x,y+h );
    ctx.arc( x,y+h/2, h/2, 0.5*Math.PI, 1.5*Math.PI );
}

function add_mouse_listener_to_canvas(canvas,ctx){

    $(".familypage_dom_hovermenu").detach().appendTo(document.documentElement);

    canvas.onmousemove = function(e) {

        // get mouse position in terms of coordinates
        // that were used for drawing on the canvas
        var rect = this.getBoundingClientRect(),
        x = e.clientX - rect.left,
        y = e.clientY - rect.top;
            
        // make caption invisible by default
        var hovermenu = $('.familypage_dom_hovermenu');
        hovermenu.removeClass('visible');
        
        // check for domain graphics at the mouse potiions, 
        // starting with the "top" (most visible) domains
        i = ctx.domains.length-1;
        while(d = ctx.domains[i--]) {
            build_path_for_domain_on_canvas( ctx, d[0], d[1], d[2], d[3] );
            if( ctx.isPointInPath(x, y) ){
                
                // got a hit, show caption  and stop checking domains
                hovermenu.addClass('visible');
                hovermenu.css({top: e.pageY+10, left: e.pageX+10});
                $('#familypage_dom_hovermenu_title').html( d[4] )
                $('#familypage_dom_hovermenu_desc').html( d[5] )
                break;
            }
        }

    };

    canvas.onmouseout = function(e){
        $('.familypage_dom_hovermenu').removeClass('visible')
    }
}
