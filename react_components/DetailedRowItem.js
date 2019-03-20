import React, {Component} from 'react';
import Util from "./Util";

export default class DetailedRowItem extends Component {

    constructor(props) {
        super(props);

        const {value} = this.props;

        this.id = Math.floor((Math.random() * 100) + 1);
        this.isUnmounted = false;
        this.state = {
            isEditing: false,
            value
        };
    }

    componentWillReceiveProps(props) {
        this.props = props;

        const {value, type} = this.props;

        if (type === 'select') {
            this.setSelectBoxText(value);
        }
    }

    componentDidMount() {
        const {type, placeholder} = this.props;

        var $ = jQuery;

        if (type === 'date') {
            $(`.flatpickr-input#input-${this.id}`).flatpickr({
                dateFormat: 'Y-m-d',
                onChange: (selectedDates, dateStr, instance) => {
                    this.onDateInputUpdate(dateStr);
                }
            });
        } else if (type === 'select') {
            var $elem = $(`#select-${this.id}`);

            $elem.chosen({
                scroll_to_highlighted: false,
                disable_search_threshold: 4,
                width: "100%",
                placeholder: placeholder
            });

            $elem.on('change', () => {
                this.updateState({
                    value: $elem.val()
                }, () => {
                    this.onSelectChange();
                });
            });

            this.setSelectBoxText($elem.val());
        }
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    openPopup() {
        alert();
    }

    inputClick() {
        this.updateState({
            isEditing: true
        });
    }

    onInputUpdate(e) {
        this.updateState({
            value: e.target.value
        });
    }

    onDateInputUpdate(value) {
        const {onUpdate} = this.props;

        this.updateState({
            value: Util.getDateHuman(value, true)
        });

        onUpdate(value);
    }

    onInputBlur(e) {
        const {onUpdate} = this.props;
        const {value} = this.state;

        this.updateState({
            isEditing: false
        });

        onUpdate(value);
    }

    onSelectChange(e = false) {
        const {onUpdate} = this.props;
        let {value} = this.state;

        if (e) {
            value = e.target.value;

            this.updateState({
                value: e.target.value
            });
        }

        this.updateState({
            isEditing: false,
        });

        this.setSelectBoxText(value);

        onUpdate(value);
    }

    setSelectBoxText(value) {
        const {items} = this.props;
        let {text} = this.state;

        text = undefined;

        for (let i in items) {
            let item = items[i];

            if ((item.value + "") === (value + "")) {
                text = item.text;
            }
        }

        this.updateState({
            text
        });
    }

    render() {
        const {type, label, icon, prefix, suffix, items, isMultiple, placeholder, showText, labelClass} = this.props;
        const {isEditing, value, text} = this.state;

        let {isLabel} = this.props;

        if (isLabel === undefined)
            isLabel = true;

        return (
            <div className="detail-row">
                <div className="col-md-6">
                    <div className="table-container">
                        {icon &&
                        <div className="table-cell icon"><i
                            className={icon}/></div>
                        }
                        <div className="table-cell">{label}</div>
                    </div>
                </div>
                <div className="col-md-6 text-right">
                    {type === 'label' &&
                    <span className={`${isLabel ? `label ${labelClass}` : ''}`}>{value}</span>
                    }
                    {type === 'input' &&
                    <div>
                        {isEditing &&
                        <input
                            type="text"
                            value={value}
                            onBlur={this.onInputBlur.bind(this)}
                            onChange={this.onInputUpdate.bind(this)}/>
                        }
                        {!isEditing &&
                        <span onClick={this.inputClick.bind(this)}
                              className={`${isLabel ? `label ${labelClass}` : ''}`}>{prefix ? prefix : ''}{value}{suffix ? suffix : ''}</span>
                        }
                    </div>
                    }
                    {type === 'textarea' &&
                    <div>
                        {isEditing &&
                        <textarea
                            value={value}
                            onBlur={this.onInputBlur.bind(this)}
                            onChange={this.onInputUpdate.bind(this)}/>
                        }
                        {!isEditing &&
                        <span onClick={this.inputClick.bind(this)} className={`${isLabel ? `label ${labelClass}` : ''}`}>{value}</span>
                        }
                    </div>
                    }
                    {type === 'select' && isMultiple &&
                    <div>
                        <select
                            id={`select-${this.id}`}
                            value={value}
                            className="chosen-select"
                            data-placeholder={placeholder}
                            multiple={isMultiple}
                            onChange={this.onSelectChange.bind(this)}>
                            <option value={""}>{placeholder}</option>
                            {items.map((item, index) => (
                                <option key={index} value={item.value}>{item.text}</option>
                            ))}
                        </select>
                    </div>
                    }
                    {type === 'select' && !isMultiple &&
                    <div>
                        {isEditing &&
                        <select
                            value={value}
                            id={`select-${this.id}`}
                            onChange={this.onSelectChange.bind(this)}>
                            {placeholder &&
                            <option value={""}>{placeholder}</option>
                            }
                            {items.map((item, index) => (
                                <option key={index} value={item.value}>{item.text}</option>
                            ))}
                        </select>
                        }
                        {!isEditing &&
                        <span onClick={this.inputClick.bind(this)}
                              className={`${isLabel ? `label ${labelClass}` : ''}`}>{showText ? (text ? text : placeholder) : value}</span>
                        }
                    </div>
                    }
                    {type === 'date' &&
                    <div>
                        <input
                            id={`input-${this.id}`}
                            type="text"
                            value={value}
                            className={`datepicker flatpickr-input`}
                            readOnly={true}
                            required={true}
                            //onBlur={this.onInputBlur.bind(this)}
                            onChange={this.onInputUpdate.bind(this)}/>
                    </div>
                    }
                    {type === 'popup' &&
                    <span onClick={this.openPopup.bind(this)} className={`${isLabel ? `label ${labelClass}` : ''}`}>{value}</span>
                    }
                </div>
            </div>
        );
    }
}