$(function () {
    //切换金额
    $(document).on("click", ".row-price label[data-type]", function () {
        $(".row-price label[data-type]").removeClass("active");
        $(this).addClass("active");
        $("input[name=price]").val($(this).data("value"));
        if ($(this).data("type") === 'custom') {
            if ($("input", this).size() == 0) {
                $(this).html('<input type="number" name="customprice" class="form-control customprice" />');
            }
            $("input[name=customprice]").trigger("focus").trigger("keydown");
        } else {
            $(".row-price label[data-type='custom']").html('其它金额');
        }

        $("#anonymoustips").toggleClass("hidden", $(this).data("value") == "0");
        $("#c-isanonymous").prop("checked", false).prop("disabled", $(this).data("value") != "0")
    });

    //选中价格
    $(document).on("click", ".row-paytype label", function () {
        $(".row-paytype label").removeClass("active");
        $(this).addClass("active");
        $("input[name=paytype]").val($(this).data("value"));
    });

    //自定义价格
    $(document).on("keyup keydown change", ".customprice", function () {
        $("input[name=price]").val($(this).val());
    });

    //付费邀请设定
    $(document).on("click", ".btn-invite-user-pay", function () {
        var tips = "<div class='alert alert-danger small'>温馨提示：<br>1、被邀请者回答提问后将直接得到<b>邀请赏金</b>，与是否采纳无关<br>2、如果被邀请者在采纳最佳答案前仍未回答，赏金将退还给邀请者<br></div>";
        var price = Config.inviteprice.split(/\-/);
        layer.prompt({
            title: '付费邀请',
            content: tips + '<div class="mb-2">请输入邀请赏金 <span class="text-muted small">金额必须在￥' + Config.inviteprice + '区间</span></div><input type="text" class="layui-layer-input form-control" placeholder="金额必须在￥' + Config.inviteprice + '区间" style="width:100%;" value="' + price[0] + '">',
            area: isMobile ? 'auto' : ["460px", 'auto'],
            btn: ["确定", "取消"]
        }, function (value, index, elem) {
            $("input[name=inviteprice]").val(value);
            if (value == 0) {
                $("#invitetips").html("");
            } else {
                $("#invitetips").html("付费邀请赏金为￥" + value + "元");
            }
            layer.close(index);
        });
    });

    //标签选择
    ASK.render.tags("#c-tags");
    //编辑器
    ASK.render.editor("#c-content");
    //问题搜索
    ASK.render.question("#c-title");
    //表单提交
    ASK.api.form(".post-form", function (data, ret) {
        ASK.api.msg(ret.msg, ret.url);
        return false;
    });
});