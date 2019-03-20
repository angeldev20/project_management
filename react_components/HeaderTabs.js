import React, {Component} from 'react';
import {Link} from "react-router-dom";

export default class HeaderTabs extends Component {
    render() {
        const {tabs, location} = this.props;

        return (
            <div className="dashboard-header text-center" style={{padding: 0}}>
                <ul className="header-tabs">
                    {tabs.map(({link, text, tag, isExternal}, index) => (
                        <li id={`tab-item-${index}`}
                            data-tag={tag ? tag : ''}
                            className={(location.pathname.indexOf(link) === 0 ? "active" : "") + (tag ? " has-tag" : "")}
                            key={link}>
                            {isExternal &&
                            <a href={link}>{text}</a>
                            }
                            {!isExternal &&
                            <Link to={link}>{text}</Link>
                            }
                        </li>
                    ))}
                </ul>
            </div>
        );
    }
}