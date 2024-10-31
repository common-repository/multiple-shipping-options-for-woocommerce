// JTV

function mso_jtv_create_dom(json, mso_jtv_show_generate_button = false) {
    function impl(json, parent) {
        var mso_jtv_append_element = (parent, tag) => {
            var e = document.createElement(tag);
            parent.appendChild(e);
            return e;
        };

        var mso_jtv_create_element = (tag, mso_jtv_class_name, mso_jtv_text_content) => {
            var e = document.createElement(tag);
            e.className = mso_jtv_class_name;
            if (mso_jtv_text_content)
                e.textContent = mso_jtv_text_content;
            return e;
        };

        var mso_jtv_append_text = (element, text) => {
            element.appendChild(document.createTextNode(text));
        }

        var mso_jtv_json_escaped = /\\(?:"|\\|b|f|n|r|t|u[0-1a-fA-F]{4})/;
        switch (typeof (json)) {
            case 'boolean':
            case 'number':
                var str = JSON.stringify(json);
                var e = mso_jtv_create_element('span', 'mso_jtv_numeric_value', str);
                e.dataset.valueData = str;
                parent.appendChild(e);
                break;
            case 'string':
                var str = JSON.stringify(json);
                var str = str.substring(1, str.length - 1);
                var inner = mso_jtv_create_element('span', 'mso_jtv_string_value', '"' + str + '"');
                inner.dataset.valueData = str;
                if (mso_jtv_json_escaped.test(str)) {
                    var outer = document.createElement('span');
                    outer.appendChild(inner);
                    parent.appendChild(outer);
                } else {
                    parent.appendChild(inner);
                }
                break;
            case 'object':
                if (json === null) {
                    var e = mso_jtv_create_element('span', 'mso_jtv_show_null_value', 'null');
                    e.dataset.valueData = 'null';
                    parent.appendChild(e);
                    break;
                }

            function mso_jtv_show_add_copy_button(element, json) {
                const button = mso_jtv_append_element(element, 'div');
                button.className = 'mso_jtv_copy';
                button.addEventListener('click', (event) => {
                    const onFail = (e) => {
                        button.classList.add('mso_jtv_not_copied');
                        void button.offsetWidth; // triggers animation transitions
                        button.classList.remove('mso_jtv_not_copied');
                        console.log('Failed to copy to clipboard: ', e);
                    };
                    try {
                        navigator.clipboard.writeText(JSON.stringify(json, null, '  '))
                            .then(
                                () => {
                                    button.classList.add('mso_jtv_copied');
                                    void button.offsetWidth; // triggers animation transitions
                                    button.classList.remove('mso_jtv_copied');
                                },
                                onFail
                            );
                    } catch (e) {
                        onFail(e.message);
                    }
                });
            }

            function mso_jtv_show_create_number_of_elements(count) {
                var e = mso_jtv_create_element('span', 'mso_jtv_number_of_elements');
                e.dataset.itemCount = count;
                return e;
            }

                var isArray = Array.isArray(json);
                if (isArray) {
                    if (json.length == 0) {
                        mso_jtv_append_text(parent, '[]');
                        break;
                    }
                    mso_jtv_append_text(parent, '[');
                    var list = mso_jtv_append_element(parent, 'ul');
                    var item = null;
                    for (var i = 0; i != json.length; ++i) {
                        if (item)
                            mso_jtv_append_text(item, ',');
                        item = document.createElement('li');
                        var outer = mso_jtv_append_element(item, 'div');
                        outer.className = 'mso_key';
                        const value = json[i];
                        mso_jtv_append_element(outer, 'span');
                        if (mso_jtv_show_generate_button)
                            mso_jtv_show_add_copy_button(outer, value);
                        impl(value, item);
                        list.appendChild(item);
                    }
                    parent.appendChild(mso_jtv_show_create_number_of_elements(json.length));
                    mso_jtv_append_text(parent, ']');
                } else {
                    var keys = Object.keys(json);
                    if (keys.length == 0) {
                        mso_jtv_append_text(parent, '{}');
                        break;
                    }
                    mso_jtv_append_text(parent, '{');
                    var list = mso_jtv_append_element(parent, 'ul');
                    var item = null;
                    for (var key of keys) {
                        if (item)
                            mso_jtv_append_text(item, ',');
                        item = document.createElement('li');
                        var outer = mso_jtv_append_element(item, 'div');
                        outer.className = 'mso_key';
                        const value = json[key];
                        var inner = mso_jtv_append_element(outer, 'span');
                        if (mso_jtv_show_generate_button)
                            mso_jtv_show_add_copy_button(outer, value);
                        inner.dataset.keyData = key;
                        inner.textContent = '"' + key + '"';
                        mso_jtv_append_text(item, ': ');
                        impl(value, item);
                        list.appendChild(item);
                    }
                    parent.appendChild(mso_jtv_show_create_number_of_elements(keys.length));
                    mso_jtv_append_text(parent, '}');
                }
                if (parent.tagName == 'LI') {
                    parent.classList.add('mso_folder', 'mso_folded');
                }
                break;
            default:
                mso_jtv_append_text(parent, 'unexpected: ' + JSON.stringify(json));
                break;
        }
    };
    var holder = document.createElement('div');
    holder.className = 'mso_jtv';
    impl(json, holder);
    for (var e of holder.querySelectorAll('li.mso_folder > div.mso_key > span')) {
        e.addEventListener('click', function (event) {
            var parent = this.parentElement.parentElement;
            var expanded = !parent.classList.toggle('mso_folded');
            if (event.ctrlKey || event.metaKey) {
                var children = parent.querySelectorAll('li.mso_folder');
                if (expanded) {
                    for (var e of children)
                        e.classList.remove('mso_folded');
                } else {
                    for (var e of children)
                        e.classList.add('mso_folded');
                }
            }
        });
    }

    return holder;
}

function mso_jtv_show_data(json) {
    try {
        json = JSON.stringify(json);
        json = JSON.parse(json);
        document.getElementById('mso_jtv_parse_error').textContent = '';
    } catch (e) {
        document.getElementById('mso_jtv_parse_error').textContent = e.message;
        return;
    }
    var tree = mso_jtv_create_dom(json, true);
    var holder = document.getElementById('mso_api_json_response');
    holder.removeChild(holder.querySelector('*'));
    holder.appendChild(tree);
}

function mso_api_response(json, event) {
    event.preventDefault();
    jQuery('.mso_logs_overly').css({'opacity': 1, 'display': 'block'});
    jQuery("#mso_api_json_response").empty();
    var tree = mso_jtv_create_dom(json, true);
    document.getElementById('mso_api_json_response').appendChild(tree);
    mso_jtv_show_data(json);
}