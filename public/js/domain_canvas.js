function draw_domain_on_canvas( ctx, x,y,w,h, color, label ){

    var grd = ctx.createLinearGradient(x, y, x, y+h);
    grd.addColorStop(0, "white");
    grd.addColorStop(.5, color);
    grd.addColorStop(1, "white");
    ctx.fillStyle = grd;
    ctx.strokeStyle = color;   

    [true,false].forEach( function(fill) {

        ctx.beginPath();
        ctx.moveTo( x,y );
        ctx.lineTo( x+w,y );
        ctx.arc( x+w,y+h/2, h/2, 1.5*Math.PI, 0.5*Math.PI );
        ctx.lineTo( x,y+h );
        ctx.arc( x,y+h/2, h/2, 0.5*Math.PI, 1.5*Math.PI );

        if( fill ){
            ctx.fill();
        } else {
            ctx.stroke();   
        }
    });

    ctx.fillStyle = 'black';
    ctx.fillText(label, x+2, h-4);
}