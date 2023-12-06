import React from "react";
import { Modal, Input, Form, Select } from "antd";
import { FormattedMessage } from "react-intl";
import config from "../../../utils/config";
import messages from "./messages";
import "./index.less";

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
export class ModalEditMember extends React.PureComponent {
  handlerEditMember = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.props.handlerEditMember(values);
    });
  };

  componentWillReceiveProps(nextProps) {
    if (this.props.visibleUpdateMember != nextProps.visibleUpdateMember) {
      this.props.form.resetFields();
    }
  }

  render() {
    const {
      updatingMember,
      visibleUpdateMember,
      record,
      recordApartment,
      language,
      setState,
    } = this.props;
    const { getFieldDecorator, getFieldValue } = this.props.form;
    return (
      <Modal
        title={
          <FormattedMessage
            {...messages.modalEditMemberTitle}
            values={{
              apartmentName: recordApartment.name,
            }}
          />
        }
        visible={visibleUpdateMember}
        onOk={this.handlerEditMember}
        onCancel={() => {
          if (updatingMember) return;
          setState({
            visibleUpdateMember: false,
          });
        }}
        okText={<FormattedMessage {...messages.update} />}
        cancelText={<FormattedMessage {...messages.deleteMemberCancelText} />}
        okButtonProps={{ loading: updatingMember }}
        cancelButtonProps={{ disabled: updatingMember }}
        maskClosable={false}
      >
        <Form {...formItemLayout}>
          <Form.Item
            label={<FormattedMessage {...messages.memberPhone} />}
            colon={false}
          >
            {getFieldDecorator("resident_phone", {
              initialValue: record && `0${record.phone.slice(-9)}`,
              rules: [
                {
                  required: true,
                  message: <FormattedMessage {...messages.phoneRequired} />,
                  whitespace: true,
                },
              ],
            })(<Input disabled={true} maxLength={10} />)}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.memberName} />}
            colon={false}
          >
            {getFieldDecorator("resident_name", {
              initialValue: record && record.first_name,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.memberNameRequired} />
                  ),
                  whitespace: true,
                },
              ],
            })(
              <Input
                disabled={
                  record && !!record.first_name && record.first_name.length > 0
                }
              />
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.memberRole} />}
            colon={false}
          >
            {getFieldDecorator("type", {
              initialValue: `${record && record.type}`,
              rules: [
                {
                  required: true,
                  message: (
                    <FormattedMessage {...messages.memberRoleRequired} />
                  ),
                  whitespace: true,
                },
              ],
            })(
              <Select
                showSearch
                placeholder={
                  <FormattedMessage {...messages.statusPlaceholder} />
                }
                optionFilterProp="children"
                filterOption={(input, option) =>
                  option.props.children
                    .toLowerCase()
                    .indexOf(input.toLowerCase()) >= 0
                }
              >
                {config.TYPE_RESIDENT.map((gr) => {
                  return (
                    <Select.Option key={`group-${gr.id}`} value={`${gr.id}`}>
                      {language === "en" ? gr.name_en : gr.name}
                    </Select.Option>
                  );
                })}
              </Select>
            )}
          </Form.Item>
          {getFieldValue("type") != 1 && getFieldValue("type") != 2 && (
            <Form.Item
              label={<FormattedMessage {...messages.memberRelationship} />}
            >
              {getFieldDecorator("type_relationship", {
                initialValue: record ? record.type_relationship : 7,
                rules: [
                  {
                    required: true,
                    message: (
                      <FormattedMessage
                        {...messages.memberRelationshipRequired}
                      />
                    ),
                    whitespace: true,
                    type: "number",
                  },
                ],
              })(
                <Select
                  placeholder={
                    <FormattedMessage
                      {...messages.memberRelationshipPlaceholder}
                    />
                  }
                  optionFilterProp="children"
                >
                  {config.RELATIONSHIP_APARTMENT.map((gr) => {
                    return (
                      <Select.Option key={`group-${gr.id}`} value={gr.id}>
                        {language === "en" ? gr.title_en : gr.title}
                      </Select.Option>
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

export default ModalEditMember;
