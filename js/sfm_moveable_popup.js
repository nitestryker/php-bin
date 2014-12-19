var _sfm_tracker=null;

function SFM_MoveablePopup(div_obj)
{
    var _div_obj = div_obj;
    //div_obj.handlerobj = this;

    var downposX = 0;
    var downposY = 0;
    var dragging = false;

    this.isIE    = false;
    this.isNS    = false;


    this.Init = function()
    {
        if (navigator.userAgent.indexOf("MSIE") >= 0 ||
            navigator.userAgent.indexOf("Opera") >= 0) 
        {
            this.isIE = true;
        }
        else
        {
            this.isNS = true;
        }
    }


    _div_obj.onmousedown = function(event)
    {
        var x=0;
        var y=0;

        _this = this.handlerobj;

        if (_this.isIE) 
        {
            x = window.event.clientX + 
                document.documentElement.scrollLeft + 
                document.body.scrollLeft;

            y = window.event.clientY + 
                document.documentElement.scrollTop + 
                document.body.scrollTop;
        }
        else
        {
            x = event.clientX + window.scrollX;
            y = event.clientY + window.scrollY;
        }

        _this.cursorStartX = x;
        _this.cursorStartY = y;
        _this.divStartX   = parseInt(this.style.left, 10);
        _this.divStartY   = parseInt(this.style.top,  10);

        if (this.handlerobj.isIE) 
        {
            document.attachEvent("onmousemove", _this.onmousemove);
            document.attachEvent("onmouseup",   _this.onmouseup);
            window.event.cancelBubble = true;
            window.event.returnValue = false;
        }
        else
        {
            document.addEventListener("mousemove", _this.onmousemove,   true);
            document.addEventListener("mouseup",   _this.onmouseup, true);
            event.preventDefault();
        }

        _sfm_tracker = _this;
        _this._div_obj = this;

    }

    this.onmousemove = function(event)
    {
        _this = _sfm_tracker;

        var x = 0;
        var y = 0;


        if (_this.isIE) 
        {
            x = window.event.clientX + document.documentElement.scrollLeft
              + document.body.scrollLeft;
            y = window.event.clientY + document.documentElement.scrollTop
              + document.body.scrollTop;
        }
        else
        {
            x = event.clientX + window.scrollX;
            y = event.clientY + window.scrollY;
        }

        _this._div_obj.style.left = (_this.divStartX + x - _this.cursorStartX) + "px";
        _this._div_obj.style.top  = (_this.divStartY   + y - _this.cursorStartY) + "px";

        if (_this.isIE) 
        {
            window.event.cancelBubble = true;
            window.event.returnValue = false;
        }
        else
        {
            event.preventDefault();
        }
    }

    this.onmouseup = function()
    {
         _this = _sfm_tracker;
        if (_this.isIE) 
        {
            document.detachEvent("onmousemove", _this.onmousemove);
            document.detachEvent("onmouseup",   _this.onmouseup);
        }
        else
        {
            document.removeEventListener("mousemove", _this.onmousemove,   true);
            document.removeEventListener("mouseup",   _this.onmouseup, true);
        }
        _sfm_tracker = null;
        
    }

   this.Init();
}