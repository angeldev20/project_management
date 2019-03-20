import React, {Component} from 'react';

export default class EmptyContent extends Component {
    render() {
        const {title, description, button} = this.props;

        return (
            <div className="empty-content-text">
                <div className="row text-center">
                    <img src="/assets/blueline/images/empty-icon.svg" width="75" alt="empty-icon"/>
                    <h4>{title}</h4>
                    <p>{description}</p>
                    {button}
                </div>
            </div>
        );
    }
}