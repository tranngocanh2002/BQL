import React from "react";
import PropTypes from "prop-types";
import { connect } from "react-redux";
import { FormattedMessage, injectIntl } from "react-intl";
import messages from "../messages";
import { Modal, TreeSelect, Input, Form, Select, Spin } from "antd";
import _ from "lodash";
import { fetchApartmentAction, fetchMemberAction } from "./actions";

import("./index.less");
const TreeNode = TreeSelect.TreeNode;
const formItemLayout = {
  labelCol: {
    span: 8,
  },
  wrapperCol: {
    span: 12,
  },
};
/* eslint-disable react/prefer-stateless-function */
@Form.create()
export class ModalActiveCard extends React.PureComponent {
  constructor(props) {
    super(props);
    this.state = {
      currentSelected: -1,
    };
    this._onSearch = _.debounce(this.onSearch, 300);
    this._onSearch2 = _.debounce(this.onSearch, 300);
  }
  handleChangeConfig = (e) => {
    this.setState({ currentSelected: e });
    if (e) {
      this.props.dispatch(fetchMemberAction({ apartment_id: e }));
    }
  };

  handlerUpdate = () => {
    const { currentEdit, form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.props.handlerUpdate &&
        this.props.handlerUpdate({
          ...values,
          id: currentEdit.id,
        });
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visible != nextProps.visible) {
      this.props.form.resetFields();
    }
  }
  // componentDidMount() {
  //   this.props.dispatch(fetchMemberAction());
  // }

  onSearch = (keyword) => {};
  render() {
    const { creating, visible, setState, currentEdit, cardDetail } = this.props;
    const { lst, loading } = cardDetail.apartments;
    const { lst2 } = cardDetail.members;
    const { getFieldDecorator, getFieldValue } = this.props.form;
    const formatMessage = this.props.intl.formatMessage;
    return (
      <Modal
        title={formatMessage(messages.editCard)}
        visible={visible}
        onOk={this.handlerUpdate}
        onCancel={() => {
          if (creating) return;
          setState({
            visibleAddCard2: false,
          });
        }}
        okText={formatMessage(messages.update)}
        cancelText={formatMessage(messages.cancelText)}
        okButtonProps={{ loading: creating }}
        cancelButtonProps={{ disabled: creating }}
        maskClosable={false}
        width="40%"
      >
        <Form
          {...formItemLayout}
          className="notificationPage"
          labelAlign="left"
        >
          <Form.Item label={formatMessage(messages.property)}>
            {getFieldDecorator("apartment_id", {
              // initialValue:
              //   (currentEdit && String(currentEdit.apartment_id)) ||
              //   this.state.addApartment_id
              //     ? String(this.state.addApartment_id)
              //     : undefined,
              rules: [
                {
                  required: true,
                  message: formatMessage(messages.errorEmpty, {
                    field: formatMessage(messages.property),
                  }),
                  whitespace: true,
                },
              ],
            })(
              <Select
                allowClear
                loading={loading}
                showSearch
                placeholder={formatMessage(messages.searchProperty)}
                optionFilterProp="children"
                notFoundContent={loading ? <Spin size="small" /> : null}
                onSearch={this._onSearch}
                onChange={(value, opt) => {
                  if (!opt) {
                    this._onSearch("");
                  }
                  this.handleChangeConfig(value);
                }}
              >
                {lst.map((gr) => {
                  return (
                    <Select.Option
                      key={`group-${gr.id}`}
                      value={`${gr.id}`}
                    >{`${gr.name} (${gr.parent_path})`}</Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          {getFieldValue("apartment_id") && (
            <Form.Item label={formatMessage(messages.cardOwner)}>
              {getFieldDecorator("resident_user_id", {
                // initialValue:
                //   (currentEdit && String(currentEdit.apartment_id)) ||
                //   this.state.addApartment_id
                //     ? String(this.state.addApartment_id)
                //     : undefined,
                rules: [
                  {
                    required: true,
                    message: formatMessage(messages.errorEmpty, {
                      field: formatMessage(messages.cardOwner),
                    }),
                    whitespace: true,
                  },
                ],
              })(
                <Select
                  allowClear
                  loading={cardDetail.members.loading}
                  showSearch
                  onSearch={this._onSearch2}
                  placeholder={formatMessage(messages.searchProperty)}
                  optionFilterProp="children"
                  notFoundContent={
                    cardDetail.members.loading ? <Spin size="small" /> : null
                  }
                  onChange={(value, opt) => {
                    if (!opt) {
                      this._onSearch2("");
                    }
                  }}
                >
                  {lst2.map((gr) => {
                    return (
                      gr.deleted_at == null && (
                        <Select.Option
                          key={`group-${gr.apartment_map_resident_user_id}`}
                          value={`${gr.apartment_map_resident_user_id}`}
                        >
                          {gr.first_name}
                        </Select.Option>
                      )
                    );
                  })}
                </Select>
              )}
            </Form.Item>
          )}
        </Form>
      </Modal>
    );
  }
}

export default injectIntl(ModalActiveCard);
