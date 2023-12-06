import { AutoComplete, Button, Form, Input, Modal, Select } from "antd";
import React from "react";
import { FormattedMessage } from "react-intl";
import { validateName } from "utils";
import { regexPhoneNumberVN } from "utils/constants";
import { config } from "../../../utils";
import { fetchAllResidentByPhoneAction } from "./actions";
import messages from "./messages";
import WithRole from "components/WithRole";
import("./index.less");

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
export class ModalAddMember extends React.PureComponent {
  handlerAddMember = () => {
    const { form } = this.props;
    const { validateFieldsAndScroll } = form;
    validateFieldsAndScroll((errors, values) => {
      if (errors) {
        return;
      }
      this.props.handlerAddMember(values);
    });
  };

  handlerCancel = () => {
    if (this.props.addingMember) return;
    this.props.setState({
      visibleAddMember: false,
    });
  };

  UNSAFE_componentWillReceiveProps(nextProps) {
    if (this.props.visibleAddMember != nextProps.visibleAddMember) {
      this.props.form.resetFields();
      this.props.dispatch(fetchAllResidentByPhoneAction({}));
    }
  }

  render() {
    const {
      addingMember,
      visibleAddMember,
      recordApartment,
      allResident,
      language,
    } = this.props;
    const { getFieldDecorator, getFieldValue, setFieldsValue } =
      this.props.form;
    const options = allResident.data.map((res) => `0${res.phone.slice(-9)}`);
    return (
      <Modal
        title={
          <FormattedMessage
            {...messages.modalAddMemberTitle}
            values={{ apartmentName: recordApartment.name }}
          />
        }
        visible={visibleAddMember}
        onOk={this.handlerAddMember}
        onCancel={this.handlerCancel}
        footer={[
          <WithRole
            key="addMember"
            roles={[config.ALL_ROLE_NAME.RESIDENT_MANAGEMENT_CREATE]}
          >
            <Button
              loading={addingMember}
              ghost
              type="primary"
              icon="user-add"
              style={{
                position: "absolute",
                left: 24,
              }}
              onClick={() => {
                this.props.history.push("/main/resident/add", {
                  apartment_id: recordApartment.id,
                  apartment_name: recordApartment.name,
                });
              }}
            >
              <FormattedMessage {...messages.addResident} />
            </Button>
          </WithRole>,
          <Button
            key="back"
            loading={addingMember}
            onClick={this.handlerCancel}
          >
            <FormattedMessage {...messages.deleteMemberCancelText} />
          </Button>,
          <Button
            key="submit"
            loading={addingMember}
            type="primary"
            onClick={this.handlerAddMember}
          >
            <FormattedMessage {...messages.addNew} />
          </Button>,
        ]}
        maskClosable={false}
      >
        <Form {...formItemLayout} labelAlign={"left"}>
          <Form.Item
            label={<FormattedMessage {...messages.memberPhone} />}
            colon={false}
          >
            {getFieldDecorator("resident_phone", {
              rules: [
                {
                  required: true,
                  message: <FormattedMessage {...messages.phoneRequired} />,
                },
                // {
                //   pattern: regexPhoneNumberVN,
                //   message: <FormattedMessage {...messages.invalidPhone} />,
                // },
                {
                  validator: (rule, value, callback) => {
                    if (!options.includes(value) && value.trim().length > 0) {
                      callback(
                        <FormattedMessage {...messages.phoneNotExisted} />
                      );
                    } else {
                      callback();
                    }
                  },
                },
              ],
            })(
              <AutoComplete
                style={{ width: "100%" }}
                dropdownMenuStyle={{ overflowY: "auto", maxHeight: 150 }}
                dataSource={options}
                onChange={(e) => {
                  setFieldsValue({
                    resident_name: "",
                  });
                  if (e.trim().length === 10) {
                    const phoneData = allResident.data.find(
                      (mm) => `0${mm.phone.slice(-9)}` == e
                    );
                    if (phoneData) {
                      setFieldsValue({
                        resident_name: phoneData.first_name,
                      });
                    }
                  }

                  if (e.trim().length > 2 && e.trim().length < 14) {
                    this.props.dispatch(
                      fetchAllResidentByPhoneAction({ phone: e })
                    );
                  } else if (e.trim().length == 0) {
                    this.props.dispatch(
                      fetchAllResidentByPhoneAction({ phone: "" })
                    );
                  }
                }}
                onSelect={(e) => {
                  setFieldsValue({
                    resident_name: allResident.data.filter(
                      (mm) => `0${mm.phone.slice(-9)}` == e
                    )[0].first_name,
                  });
                }}
              >
                <Input maxLength={10} />
              </AutoComplete>
            )}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.memberName} />}
            colon={false}
          >
            {getFieldDecorator("resident_name", {
              // rules: [
              //   {
              //     required: true,
              //     message: (
              //       <FormattedMessage {...messages.memberNameRequired} />
              //     ),
              //     whitespace: true,
              //   },
              //   {
              //     validator: (rule, value, callback) => {
              //       if (value && value.trim() != "" && !validateName(value)) {
              //         callback(<FormattedMessage {...messages.invalidName} />);
              //       } else {
              //         callback();
              //       }
              //     },
              //   },
              // ],
            })(<Input maxLength={50} disabled />)}
          </Form.Item>
          <Form.Item
            label={<FormattedMessage {...messages.memberRole} />}
            colon={false}
          >
            {getFieldDecorator("type", {
              initialValue: "1",
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
                initialValue: 7,
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

export default ModalAddMember;
