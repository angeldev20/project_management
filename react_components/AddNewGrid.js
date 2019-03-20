import React, {Component} from 'react';

export default class AddNewGrid extends Component {
    constructor(props) {
        super(props);

        this.id = 'add-new-grid-' + Math.floor((Math.random() * 100) + 1) + Math.floor((Math.random() * 100) + 1);
    }

    componentWillReceiveProps() {
        this.setup();
    }

    componentDidMount() {
        setTimeout(this.setup.bind(this), 500);
    }

    setup() {
        var $main = $(`#${this.id}`);
        var $elem = $main.find('.grid-box');

        if ($main.prev().is('.grid-box-container')) {
            var $height = $main.prev().outerHeight();
            $main.css('height', $height);
        }
    }

    render() {
        const {icon, text, onClick} = this.props;

        return (
            <div
                id={this.id}
                onClick={onClick.bind(this)}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3"}
                style={{
                    color: "#CDD8DB",
                    cursor: "pointer"
                }}>
                <div className="grid-box invoice-box" style={{
                    border: "2px solid #CDD8DB",
                    background: "none",
                    height: "100%"
                }}>
                    <div className="text-center" style={{
                        position: "absolute",
                        top: "35%",
                        right: 0,
                        bottom: 0,
                        left: 0
                    }}>
                        <i style={{
                            fontSize: "30px"
                        }} className={icon}/>
                        <h4 style={{
                            fontSize: "14px"
                        }}>{text}</h4>
                    </div>
                </div>
            </div>
        );
    }
}