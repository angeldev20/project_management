import React, {Component} from 'react';

export default class Dropdown extends Component {

    constructor(props) {
        super(props);

        this.isUnmounted = false;
        this.state = {
            selected: null,
            placeholder: 'Select',
            newItemPlaceholder: 'New Item',
            name: '',
            newItem: '',
            items: [],
            allowAdd: false
        };
    }

    componentWillReceiveProps(props) {
        this.props = props;
        this.updateState(props, () => this.bindSelection());
    }

    componentWillMount() {
        this.updateState(this.props, () => this.bindSelection());
    }

    componentWillUnmount() {
        this.isUnmounted = true;
    }

    updateState(state, callback) {
        if (!this.isUnmounted) {
            this.setState(state, callback);
        }
    }

    bindSelection() {
        const {value, items} = this.props;
        let selected = null;

        if (value) {
            items.map(item => {
                if (item.value === value) {
                    selected = item;
                }
            });
            this.updateState({selected});
        }
    }

    onSelect(selected) {
        const {name, onChange} = this.props;
        this.updateState({selected});

        if (onChange)
            onChange(name, selected.value);
    }

    onInputChange(e) {
        this.updateState({
            newItem: e.target.value
        });
    }

    onInputPress(e) {
        if (e.which === 13) {
            const {onNewItem} = this.props;
            const {newItem} = this.state;

            if (onNewItem) {
                onNewItem(newItem);
            }

            this.updateState({
                newItem: ''
            });
        }
    }

    render() {
        const {name, items, selected, placeholder, allowAdd, newItem, newItemPlaceholder} = this.state;

        return (
            <div className="custom-dropdown">
                <input type="hidden" name={name} value={selected ? selected.value : ''}/>
                <button type="button" className="dropdown-toggle" data-toggle="dropdown"
                        aria-expanded="false">
                    <span>{selected ? selected.text : placeholder}</span>
                    <i className="far fa-angle-down"/>
                </button>
                <ul className="dropdown-menu dropdown-menu--small" role="menu">
                    {items.map((item, index) => (
                        <li key={index} onClick={() => this.onSelect(item)}>{item.text}</li>
                    ))}
                    {allowAdd &&
                    <li className="new-item">
                        <input placeholder={newItemPlaceholder} type="text"
                               value={newItem} onChange={this.onInputChange.bind(this)}
                               onKeyPress={this.onInputPress.bind(this)}/>
                    </li>
                    }
                </ul>
            </div>
        );
    }
}