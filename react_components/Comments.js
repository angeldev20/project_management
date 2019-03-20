import React, {Component} from 'react';

export default class Comments extends Component {

    constructor(props) {
        super(props);

        this.state = {};
    }

    componentWillReceiveProps(props) {
        this.updateState(props);
    }

    componentWillMount() {
        this.updateState(this.props);
    }

    updateState(state, callback) {
        if (!this.isUnmounted)
            this.setState(state, callback);
    }

    render() {
        const {renderItem, data} = this.state;

        return (
            <div className="comments">
                {data.map(renderItem)}
            </div>
        );
    }
}