<div class="box box-solid">

    <div class="box-body">

        <form action="{{route('admin.tool.qrcode.post')}}" method="post">

            <input type="hidden" name="_token"  value="{{ csrf_token() }}" />
            填写要转换的文本（不能包含中文）:
            <textarea name="sourceText" id="" cols="30" rows="10"></textarea>
            <input type="submit" value="提交">
        </form>
        <hr>
        转换后的二维码为：
        @if(isset($img))

            {!! $img !!}
        @endif
    </div>
</div>
