import React, {Component} from 'react';

export default class ProgressBar extends Component {

    static classes() {
        return {
            "Draft": "",
            "Paid": "tile-progress--green",
            "Overdue": "tile-progress--red",
            "Open": "tile-progress--blue",
            "Sent": ""
        };
    }

    constructor(props) {
        super(props);

        const {progress} = this.props;

        this.id = 'progress-' + Math.floor((Math.random() * 100) + 1) + Math.floor((Math.random() * 100) + 1);
        this.state = {
            progress
        };
    }

    componentDidMount() {
        const {progress} = this.state;

        var $elem = $(`#${this.id}`).find(".progress-bar");
        $elem.animate({
            width: `${progress}%`
        });
    }

    render() {
        const {progress} = this.state;
        const {color} = this.props;

        let classes = ProgressBar.classes();

        return (
            <div id={this.id} className={`progress tile-progress ${classes[color]} tt`} title=""
                 data-original-title={progress}>
                <div className="progress-bar" role="progressbar" aria-valuenow={progress} aria-valuemin="0"
                     aria-valuemax={progress}
                     style={{
                         width: `0%`
                     }}>
                </div>
            </div>
        );
    }
}