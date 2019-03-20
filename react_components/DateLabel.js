import React, {Component} from 'react';
import Util from "./Util";

export default class DateLabel extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.id = 'datelabel-' + Math.floor((Math.random() * 100) + 1) + Math.floor((Math.random() * 100) + 1);
    }

    componentWillMount() {
        const {date} = this.props;

        this.updateState({
            date
        });
    }

    componentDidMount() {
        const {onChange} = this.props;

        var id = `#${this.id}`;
        var $elem = $(id);

        flatpickr(id, {
            dateFormat: 'M d',
            onChange: (selectedDates, dateStr, instance) => {
                $(instance.element).parent().attr("data-filled", (selectedDates.length > 0));
                onChange(selectedDates, dateStr, instance);
            }
        });
    }

    componentWillReceiveProps(props) {
        const {date} = props;

        this.updateState({
            date
        });
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    render() {
        const {date} = this.state;
        const {updateAble} = this.props;
        const human_date = date ? Util.getDateHuman(new Date(date)) : 'None';

        if (updateAble) {
            return (
                <div className="date-label" data-filled={date ? "true" : "false"}>
                    <i className="far fa-calendar-alt"/>
                    <input id={this.id} type="text" value={human_date} placeholder="Choose a Date"
                           onChange={() => {
                           }}/>
                </div>
            );
        } else {
            return <span className={"label"}>{date}</span>;
        }
    }
}