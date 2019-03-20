import React, {Component} from 'react';

class StatusLabel extends Component {

    static classes() {
        return {
            "draft": "",
            "paid": "label-success",
            "overdue": "label-important",
            "open": "label-warning",
            "sent": "label-chilled",
            "active": "label-chilled",
            "inactive": "label-default",
            "1": "label-chilled",
            "0": "label-default"
        };
    }

    render() {
        let {status, text, filled} = this.props;

        let classes = StatusLabel.classes();

        if (!text) {
            text = status;
        }
        status = status.toLowerCase();

        return (
            <span onClick={this.props.onClick}
                className={"label " + (classes[status]) + (filled ? " filled" : "")}>{text}</span>
        );
    }
}

StatusLabel.defaultProps = {
    onClick: () => {}
}

export default StatusLabel;