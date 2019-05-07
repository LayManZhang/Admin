var ASK = {

    events: {
        //请求成功的回调
        onAjaxSuccess: function (ret, onAjaxSuccess) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '操作成功';

            if (typeof onAjaxSuccess === 'function') {
                var result = onAjaxSuccess.call(this, data, ret);
                if (result === false)
                    return;
            }
            layer.msg(msg, {icon: 1});
        },
        //请求错误的回调
        onAjaxError: function (ret, onAjaxError) {
            var data = typeof ret.data !== 'undefined' ? ret.data : null;
            var msg = typeof ret.msg !== 'undefined' && ret.msg ? ret.msg : '操作失败';
            if (typeof onAjaxError === 'function') {
                var result = onAjaxError.call(this, data, ret);
                if (result === false) {
                    return;
                }
            }
            layer.msg(msg, {icon: 2});
        },
        //服务器响应数据后
        onAjaxResponse: function (response) {
            try {
                var ret = typeof response === 'object' ? response : JSON.parse(response);
                if (!ret.hasOwnProperty('code')) {
                    $.extend(ret, {code: -2, msg: response, data: null});
                }
            } catch (e) {
                var ret = {code: -1, msg: e.message, data: null};
            }
            return ret;
        }
    },
    api: {
        //获取修复后可访问的cdn链接
        cdnurl: function (url) {
            return /^(?:[a-z]+:)?\/\//i.test(url) ? url : Config.upload.cdnurl + url;
        },
        //发送Ajax请求
        ajax: function (options, success, error) {
            options = typeof options === 'string' ? {url: options} : options;
            var st, index = 0;
            st = setTimeout(function () {
                index = layer.load();
            }, 150);
            options = $.extend({
                type: "POST",
                dataType: "json",
                xhrFields: {
                    withCredentials: true
                },
                success: function (ret) {
                    clearTimeout(st);
                    index && layer.close(index);
                    ret = ASK.events.onAjaxResponse(ret);
                    if (ret.code === 1) {
                        ASK.events.onAjaxSuccess(ret, success);
                    } else {
                        ASK.events.onAjaxError(ret, error);
                    }
                },
                error: function (xhr) {
                    clearTimeout(st);
                    index && layer.close(index);
                    var ret = {code: xhr.status, msg: xhr.statusText, data: null};
                    ASK.events.onAjaxError(ret, error);
                }
            }, options);
            return $.ajax(options);
        },
        //提示并跳转
        msg: function (message, url) {
            var callback = typeof url === 'function' ? url : function () {
                if (typeof url !== 'undefined' && url) {
                    location.href = url;
                }
            };
            layer.msg(message, {
                icon: 1,
                time: 2000
            }, callback);
        },
        //表单提交事件
        form: function (elem, success, error, submit) {
            var delegation = typeof elem === 'object' && typeof elem.prevObject !== 'undefined' ? elem.prevObject : document;
            $(delegation).on("submit", elem, function (e) {
                var form = $(e.target);
                if (typeof submit === 'function') {
                    if (false === submit.call(form, success, error)) {
                        return false;
                    }
                }
                $("[type=submit]", form).prop("disabled", true);
                ASK.api.ajax({
                    url: form.attr("action"),
                    data: form.serialize(),
                    complete: function (xhr) {
                        var token = xhr.getResponseHeader('__token__');
                        if (token) {
                            $("input[name='__token__']").val(token);
                        }
                        $("[type=submit]", form).prop("disabled", false);
                    }
                }, function (data, ret) {
                    //刷新客户端token
                    if (data && typeof data.token !== 'undefined') {
                        $("input[name='__token__']").val(data.token);
                    }
                    //自动保存草稿设置
                    var autosaveKey = $("textarea[data-autosave-key]", form).data("autosave-key");
                    if (autosaveKey && localStorage) {
                        localStorage.removeItem("autosave-" + autosaveKey);
                        $(".md-autosave", form).addClass("hidden");
                    }
                    if (typeof success === 'function') {
                        if (false === success.call(form, data, ret)) {
                            return false;
                        }
                    }
                }, function (data, ret) {
                    //刷新客户端token
                    if (data && typeof data.token !== 'undefined') {
                        $("input[name='__token__']").val(data.token);
                    }
                    if (typeof error === 'function') {
                        if (false === error.call(form, data, ret)) {
                            return false;
                        }
                    }
                });
                return false;
            });
        },
        //localStorage存储
        storage: function (key, value) {
            key = key.split('.');

            var _key = key[0];
            var o = JSON.parse(localStorage.getItem(_key));

            if (typeof value === 'undefined') {
                if (o == null)
                    return null;
                if (key.length === 1) {
                    return o;
                }
                _key = key[1];
                return typeof o[_key] !== 'undefined' ? o[_key] : null;
            } else {
                if (key.length === 1) {
                    o = value;
                } else {
                    if (o && typeof o === 'object') {
                        o[key[1]] = value;
                    } else {
                        o = {};
                        o[key[1]] = value;
                    }
                }
                localStorage.setItem(_key, JSON.stringify(o));
            }
        }
    },
    render: {
        //问题搜索
        question: function (elem, options, callback) {
            var xhr;
            var question = $(elem);
            question.autoComplete($.extend({
                minChars: 1,
                menuClass: 'autocomplete-search',
                header: question.data("header") ? template(question.data("header"), {}) : '',
                footer: question.data("footer") ? template(question.data("footer"), {}) : '',
                source: function (term, response) {
                    try {
                        xhr.abort();
                    } catch (e) {
                    }
                    xhr = $.getJSON('ajax/get_search_autocomplete', {q: term, type: question.data("type")}, function (data) {
                        response(data);
                    });
                },
                renderItem: function (item, search) {
                    search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                    var regexp = new RegExp("(" + search.replace(/[\,|\u3000|\uff0c]/, ' ').split(' ').join('|') + ")", "gi");
                    template.helper("replace", function (value) {
                        return value.replace(regexp, "<b>$1</b>");
                    });
                    return template(question.data("body") ? template(question.data("body"), {}) : 'bodytpl', {item: item, search: search});
                },
                onSelect: function (e, term, item) {
                    if (typeof callback === 'function') {
                        callback.call(elem, term, item);
                    } else {
                        if ($(item).data("url")) {
                            location.href = $(item).data("url");
                        } else {
                            return false;
                        }
                    }
                }
            }, options || {}));
            //Ctrl+回车执行Bing搜索
            question.on("keydown", function (e) {
                if ($(this).attr("name") === "keyword") {
                    var form = $(this).closest("form");
                    var keyword = $(this).val();
                    var action = form.attr("action");
                    if ((e.metaKey || e.ctrlKey) && (e.keyCode == 13 || e.keyCode == 10)) {
                        form.attr("action", "https://cn.bing.com/search").attr("target", "_blank");
                        //var q = $("<input type='hidden' name='q' />").val("site:" + location.host + " " + keyword);
                        var q = $("<input type='hidden' name='q' />").val("site:" + location.host + " " + keyword);
                        form.append(q).trigger("submit");
                        setTimeout(function () {
                            form.attr("action", action).attr("target", "_self");
                            q.remove();
                        }, 100);
                    } else if (e.keyCode == 13 || e.keyCode == 10) {
                        if (question.val() == '') {
                            return false;
                        }
                        form.trigger("submit");
                    }
                }
            });
        },
        //标签搜索
        tags: function (elem, options, callback) {
            var tags = $(elem);
            //标签输入
            tags.tagsInput($.extend({
                width: 'auto',
                defaultText: '输入后回车确认',
                minInputWidth: 110,
                height: '42px',
                placeholderColor: '#999',
                onChange: function (row) {
                    if (typeof callback === 'function') {
                        callback.call(elem, row);
                    } else {
                        $(elem + "_addTag").toggle(tags.val().split(/\,/).length <= 3).focus();
                        $(elem + "_tag").trigger("blur.autocomplete").focus();
                    }
                },
                onKeyDown: function (e) {
                    if ((e.metaKey || e.ctrlKey) && (e.keyCode == 13 || e.keyCode == 10)) {
                        $(this).closest("form").trigger("submit");
                    }
                },
                autocomplete: {
                    url: 'ajax/get_tags_autocomplete',
                    minChars: 1,
                    menuClass: 'autocomplete-tags'
                }
            }, options || {}));
        },
        //编辑器绑定
        editor: function (elem) {
            var editor = $(elem);
            var form = editor.closest("form");

            //粘贴上传图片
            editor.pasteUploadImage();

            //Tab事件
            tabOverride.tabSize(4).autoIndent(true).set(editor[0]);

            //Markdown编辑器
            editor.markdown({
                resize: 'vertical', language: 'zh', iconlibrary: 'fa', autofocus: false, savable: false,
                onShow: function (e) {
                    //添加上传图片按钮
                    var imgBtn = $("button[data-handler='bootstrap-markdown-cmdImage']", e.$editor);
                    $('<input type="file" class="uploadimage" title="点击上传图片" accept="image/*" multiple="">').insertBefore(imgBtn);
                    imgBtn.parent().addClass("md-relative");
                    $(".uploadimage", e.$editor).on("mouseenter", function () {
                        imgBtn.addClass("active");
                    }).on("mouseleave", function () {
                        imgBtn.removeClass("active");
                    });
                    //自动保存草稿设置
                    var autosaveKey = editor.data("autosave-key");
                    if (autosaveKey && localStorage) {
                        autosaveKey = "autosave-" + autosaveKey;
                        var autosave = e.$editor.addClass("md-relative").prepend('<span class="md-autosave hidden"></span>').find(".md-autosave");
                        if (localStorage.getItem(autosaveKey)) {
                            autosave.html("<a href='javascript:' data-event='restore' class='text-danger'><i class='fa fa-info-circle'></i> 发现未保存的草稿数据，点击还原</a> | <a href='javascript:' data-event='release' class='text-danger'><i class='fa fa-times'></i> 清除草稿</a>").removeClass("hidden");
                        }
                        var timer = null;
                        editor.on("keyup keydown paste cut input", function () {
                            //editor.scrollTop(editor[0].scrollHeight - editor.height());
                            //editor.css('height', 'auto').css('height', editor[0].scrollHeight + offset);
                            clearTimeout(timer);
                            timer = setTimeout(function () {
                                localStorage.setItem(autosaveKey, editor.val());
                                var timeNow = new Date(),
                                    hours = timeNow.getHours(),
                                    minutes = timeNow.getMinutes(),
                                    seconds = timeNow.getSeconds();
                                var time = hours + ((minutes < 10) ? ":0" : ":") + minutes + ((seconds < 10) ? ":0" : ":") + seconds;

                                autosave.html("<i class='fa fa-info-circle'></i> 草稿已于 " + time + " 自动保存 | <a href='javascript:' data-event='release' class='text-warning'><i class='fa fa-times'></i> 清除草稿</a>").removeClass("hidden");
                            }, 3000);
                        });
                        form.on("submit", function () {
                            clearTimeout(timer);
                        });
                        autosave.on("click", "a", function () {
                            if ($(this).data("event") === 'restore') {
                                editor.val(localStorage.getItem(autosaveKey)).trigger("change");
                            } else if ($(this).data("event") === 'release') {
                                localStorage.removeItem(autosaveKey);
                            }
                            autosave.addClass("hidden");
                            return false;
                        });
                    }
                }
            });

            //手动选择上传图片
            $(".uploadimage", form).change(function () {
                $.each($(this)[0].files, function (i, file) {
                    editor.uploadFile(file, file.name);
                });
            });

            //捕获回车
            editor.keydown(function (e) {
                if ((e.metaKey || e.ctrlKey) && (e.keyCode == 13 || e.keyCode == 10)) {
                    form.trigger("submit");
                }
            });

            //@用户支持 #话题支持
            var loadingText = '加载中...';
            var userTipsText = '请输入关键词进行搜索用户';
            var questionTipsText = '请输入关键词进行搜索问题或文章';
            var ajax;
            editor.textcomplete([
                {
                    id: 'user',
                    match: /\B@(((?!\s).)*)$/,
                    search: function (term, callback) {
                        callback([loadingText]);
                        ajax && ajax.abort();
                        if (!term || term === '@') {
                            ajax = $.ajax({
                                url: "ajax/get_user_autocomplete",
                                data: {q: '', id: form.find("input[name=id]").val(), type: form.find("input[name=type]").val()},
                                dataType: 'json',
                                success: function (result) {
                                    var data = [];
                                    if (result.length > 0) {
                                        $.each(result, function (index, item) {
                                            data.push('<div data-username="' + item.username + '"><img src="' + item.avatar + '" class="img-circle mr-1" width="20" height="20">' + item.nickname + ' <span class="small text-muted">@' + item.username + '</span></div>');
                                        });
                                        callback(data, true);
                                    } else {
                                        callback([userTipsText]);
                                    }
                                }
                            });
                            return;
                        }
                        ajax = $.ajax({
                            url: "ajax/get_user_autocomplete",
                            data: {q: term, id: form.find("input[name=id]").val(), type: form.find("input[name=type]").val()},
                            dataType: 'json',
                            success: function (result) {
                                var data = [];
                                if (result.length > 0) {
                                    $.each(result, function (index, item) {
                                        data.push('<div data-username="' + item.username + '"><img src="' + item.avatar + '" class="img-circle mr-1" width="20" height="20">' + item.nickname + ' <span class="small text-muted">@' + item.username + '</span></div>');
                                    });
                                }
                                callback(data, true);
                            }
                        });
                    },
                    index: 1,
                    replace: function (word) {
                        if (word !== loadingText && word !== userTipsText) {
                            return '@' + $(word).data("username") + ' ';
                        }
                    }
                },
                {
                    id: 'question',
                    match: /\B#(((?!\s).)*)$/,
                    search: function (term, callback) {
                        if (!term || term === '#') {
                            return callback([questionTipsText]);
                        }
                        callback([loadingText]);
                        $.ajax({
                            url: "ajax/get_question_autocomplete",
                            data: {q: term},
                            dataType: 'json'
                        }).then(function (result) {
                            var data = [];
                            var regexp = new RegExp("(" + term.replace(/[\,|\u3000|\uff0c]/, ' ').split(' ').join('|') + ")", "gi");
                            if (result.length > 0) {
                                $.each(result, function (index, item) {
                                    data.push('<div class="row" data-id="' + item.id + '" data-type="' + item.type + '" data-title="' + item.title + '"><div class="col-xs-10">#<span>' + item.title.replace(regexp, "<b>$1</b>") + '</span></div>' + '<div class="col-xs-2"><span class="tag tag-xs ' + (item.type === 'question' ? "" : "tag-danger") + '">' + (item.type === 'question' ? "问题" : "文章") + '</span>' + '</div></div>');
                                });
                            }
                            callback(data, true);
                        });
                    },
                    replace: function (word) {
                        if (word !== loadingText && word !== questionTipsText) {
                            return '#' + $(word).data("id") + '[' + $(word).data("title") + ']' + '(' + ($(word).data("type") === 'question' ? 'Q' : 'A') + ')' + ' ';
                        }
                    },
                    index: 1
                }
            ], {appendTo: 'body'});
        },
        //倒计时
        countdown: function (elem) {
            var makeTimer = function (elem, timeLeft) {
                var days = Math.floor(timeLeft / 86400);
                var hours = Math.floor((timeLeft - (days * 86400)) / 3600);
                var minutes = Math.floor((timeLeft - (days * 86400) - (hours * 3600)) / 60);
                var seconds = Math.floor((timeLeft - (days * 86400) - (hours * 3600) - (minutes * 60)));

                if (hours < "10") {
                    hours = "0" + hours;
                }
                if (minutes < "10") {
                    minutes = "0" + minutes;
                }
                if (seconds < "10") {
                    seconds = "0" + seconds;
                }
                $(elem).html(days + "天" + hours + "时" + minutes + "分" + seconds + "秒");

            };

            $(elem).each(function () {
                var seconds = $(this).data("seconds");

                setInterval(function () {
                    makeTimer(elem, seconds);
                    seconds--;
                }, 1000);

            });
        }
    }
};