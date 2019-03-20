import React, {Component} from 'react';
import API from './Api.js';
import format from 'string-format';

const Label = ({type}) => {
    const color = type === 'Service' ? 'blue' : 'purple';

    return (
        <div>
            <span className={`label ${color}`} title={type}>{type}</span>
        </div>
    );
};

export default class ItemGridItem extends Component {

    constructor(props) {
        super(props);

        this.id = "expense-box-" + this.props.id;
        this.state = {
            loading: false,
            isDeleted: false
        };
    }

    onDelete(id) {
        this.setState({loading: true});

        API.get(format('items/delete_item/{0}', id))
            .then(res => res.data)
            .then(data => this.onDeleteFinish(data));
    }

    onDeleteFinish(response) {
        if (response.status)
            $("#" + this.id).fadeOut();

        this.setState({loading: false});
    }

    onClick() {

    }

    render() {
        const {id, name, value, type, description} = this.props;
        const {loading} = this.state;

        return (
            <div
                id={this.id}
                className={"grid-box-container grid__col-xs-6 grid__col-sm-6 grid__col-md-6 grid__col-lg-3" + (loading ? " loading" : "") }>
                <div className="grid-box invoice-box item-box">
                    <div className="col-md-9 status">
                        <Label type={type}/>
                    </div>
                    <div className="col-md-3 text-right ellipsis">
                        <button type="button" className="dropdown-toggle" data-toggle="dropdown"
                                aria-expanded="false">
                            <i className="far fa-ellipsis-v"/>
                        </button>
                        <ul className="dropdown-menu dropdown-menu--small" role="menu">
                            <li>
                                <a href={`/items/update_items/${id}`} data-toggle="mainmodal">Edit Project</a>
                            </li>
                            <li>
                                <a href="#" className="item-delete-trigger" data-href={`items/delete_item/${id}`}>Delete</a>
                            </li>
                            <li>
                                <a href={`/items/copy/${id}`} data-toggle="mainmodal">Duplicate</a>
                            </li>
                        </ul>
                    </div>
                    <div className="clearfix"/>
                    <div className="text-container">
                        <div className="col-md-12 amount">
                            <h4>{name ? name : ' '}</h4>
                        </div>
                        <div className="clearfix"/>
                        <div className="col-md-12 description">{description ? description : ' '}</div>
                    </div>
                    <div className="grid-footer">
                        <div className="datetime">
                            <span>${parseFloat(value).formatMoney()}</span>
                        </div>
                        <div className="trash hide">
                            <button onClick={() => this.onDelete(id)}><i className="far fa-trash-alt"/></button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}