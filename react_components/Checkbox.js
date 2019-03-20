import React, {Component} from 'react';

export default class Checkbox extends Component {

    constructor(props) {
        super(props);

        const {checked} = this.props;

        this.isUnmounted = false;
        this.id = 'checkbox-' + Math.floor((Math.random() * 100) + 1) + Math.floor((Math.random() * 100) + 1);
        this.state = {
            checked: checked
        };
    }

    componentDidMount() {
        var $elem = $(`#${this.id}`);

        $elem.find("input[type='checkbox']").on("change", this.onCheckboxChange.bind(this));
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    onCheck(e) {
        e.preventDefault();

        const {checked} = this.state;

        var $elem = $(`#${this.id}`);

        $elem.find("input[type='checkbox']").prop("checked", !checked);
        $elem.find("input[type='checkbox']").trigger("change");
    }

    onCheckboxChange(e) {
        var isChecked = $(e.target).is(":checked");

        const {onCheckChange} = this.props;

        this.updateState({
            checked: isChecked
        });

        if (onCheckChange)
            onCheckChange();
    }

    render() {
        const {checked} = this.state;
        const {checkboxAttributes} = this.props;
        const id = "check-" + this.id;

        return (
            <div id={this.id} className="custom-checkbox">
                <div className="hidden">
                    <input id={id} type="checkbox" {...checkboxAttributes}/>
                </div>
                <div className="trigger" onClick={this.onCheck.bind(this)}>
                    {!checked &&
                    <i className="far fa-square"/>
                    }
                    {checked &&
                    <i className="far fa-check-square"/>
                    }
                </div>
            </div>
        );
    }
}